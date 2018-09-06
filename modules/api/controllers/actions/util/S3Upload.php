<?php

namespace app\modules\api\controllers\actions\util;


use Yii;
use yii\base\Action;
use yii\web\UploadedFile;
use app\components\Aws;
use app\components\Image;
use app\modules\api\components\Messages;
use app\models\UploadFile;
use app\modules\api\components\ApiStatusMessages;

class S3Upload extends Action
{
    public function run()
    {
        $params = Yii::$app->request->post();
        $statusCode = ApiStatusMessages::FAILED;
        $statusMsg = null;
        $extraParams = [];

        $model = new UploadFile();
        $image = new Image();
        $model->scenario = UploadFile::SCENARIO_API_CREATE;
        $model->attributes = $params;
        $model->s3Options = null != $model->s3Options ? json_decode($model->s3Options, true) : [];
        $model->options = null != $model->options ? json_decode($model->options, true) : [];
        $fileData = @$_FILES;

        $logParams = $params;

        if (isset($logParams['fileData'])) {
            // This field contains image data as base64 encoded lengthy string. Just cutoff for avoid appending to log.
            $logParams['fileData'] = substr($logParams['fileData'], 0, 100) . '....';
        }
        Yii::$app->appLog->writeLog('Request data.', [$logParams]);

        Yii::$app->appLog->writeLog('File data.', [$fileData]);

        if ('' != @$params['fileData']) {
            list($imgAttrib, $imageData) = explode(',', $params['fileData']);
            $mimeType = str_replace(['data:','base64',";","\""], '', $imgAttrib);

            if ($mimeType == 'image/png') {
                $model->fileName = str_replace('.jpg', '.png', $model->fileName);
            }

            $tmpPath = Yii::$app->params['tempPath'] . $model->fileName;
            file_put_contents($tmpPath, base64_decode($imageData));
            $fileData['file'] = [
                'type' => $mimeType,//$image->getMimeType($tmpPath),
                'tmp_name' => $tmpPath,
            ];
        }

        if (isset($fileData['file'])) {

            $signed = true;
            if (@$model->s3Options['ACL'] == 'public-read') {
                $signed = false;
            }

            if ($model->validateModel()) {
                $aws = new Aws();
                $result = [];
                if (!$model->isImage($fileData['file']['type'])) {
                    // Just upload document files as it is
                    $result['main'] = [
                        'awsRes' => $aws->s3UploadObject($model->fileName, $fileData['file']['tmp_name'], $model->s3Options),
                        'fileName' => $model->fileName,
                        'fileUrl' => $aws->s3GetObjectUrl($model->fileName, $signed),
                    ];
                } else {
                    // Main image upload
                    if (@$model->options['compress'] == UploadFile::COMPRESS_YES) {
                        // Compress main image and upload
                        $compressFileName = 'comp_' . $model->fileName;
                        $destPath = Yii::$app->params['tempPath'] . $compressFileName;
                        if ($image->resizeByWidth($fileData['file']['tmp_name'], $destPath)) {
                            $result['main'] = [
                                'awsRes' => $aws->s3UploadObject($model->fileName, $destPath, $model->s3Options),
                                'fileName' => $model->fileName,
                                'fileUrl' => $aws->s3GetObjectUrl($model->fileName, $signed),
                                'tmpFile' => $destPath
                            ];
                        }
                    } else {
                        // No compression upload as it is
                        $result['main'] = [
                            'awsRes' => $aws->s3UploadObject($model->fileName, $fileData['file']['tmp_name'], $model->s3Options),
                            'fileName' => $model->fileName,
                            'fileUrl' => $aws->s3GetObjectUrl($model->fileName, $signed)
                        ];
                    }

                    // Thumbnail image upload
                    if (@$model->options['thumbnail'] == UploadFile::THUMBNAIL_YES) {
                        $thumbFileName = 'thumb_' . $model->fileName;
                        $destPath = Yii::$app->params['tempPath'] . $thumbFileName;
                        if ($image->resizeByWidth($fileData['file']['tmp_name'], $destPath, $model->options['thumbnailWidth'])) {
                            $result['thumb'] = [
                                'awsRes' => $aws->s3UploadObject($thumbFileName, $destPath, $model->s3Options),
                                'fileName' => $thumbFileName,
                                'fileUrl' => $aws->s3GetObjectUrl($thumbFileName, $signed),
                                'tmpFile' => $destPath
                            ];
                        }
                    }
                }

                if ($this->isAllSuccess($result)) {
                    Yii::$app->appLog->writeLog('File upload success');
                    $statusCode = ApiStatusMessages::SUCCESS;
                    $extraParams = Messages::s3FileInfo(@$result['main']['fileName'], @$result['main']['fileUrl'],
                        @$result['thumb']['fileName'], @$result['thumb']['fileUrl']);
                }

                @unlink(@$result['main']['tmpFile']);
                @unlink(@$result['thumb']['tmpFile']);
            }
        } else {
            $statusCode = ApiStatusMessages::MISSING_MANDATORY_FIELD;
        }

        $statusCode = !empty($model->statusCode) ? $model->statusCode : $statusCode;
        $statusMsg = !empty($model->statusMessage) ? $model->statusMessage : $statusMsg;

        $response = Messages::commonStatus($statusCode, $statusMsg, $extraParams);
        $this->controller->sendResponse($response);
    }

    /**
     * Check whether all uploads were succeeded
     * @param array $result AWS upload response of each file
     * @return boolean true/false
     */
    private function isAllSuccess($result)
    {
        $allSuc = true;
        foreach ($result as $res) {
            if ('' == @$res['awsRes']['ObjectURL']) {
                $allSuc = false;
            }
        }

        return $allSuc;
    }
}
?>