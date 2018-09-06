<?php

namespace app\components;

use Yii;
use yii\base\Component;
use Aws\S3\S3Client;
use Aws\Credentials\CredentialProvider;
use Aws\CognitoIdentity\CognitoIdentityClient;
use yii\base\Exception;

class Aws extends Component
{
    // Aws configurations
    public $awsConfig;

    // Credential provider
    public $cp;

    // S3 Bucket access
    public $s3;

    public function init()
    {
        // Remove
        $cp = CredentialProvider::ini('default', $this->awsConfig['credentialFilePath']);
        $cp = CredentialProvider::memoize($cp);
        $cogClient = new CognitoIdentityClient([
            'version' => 'latest',
            'region'  => $this->awsConfig['s3']['region'],
            'scheme' => 'https',
            'credentials' => $this->cp
        ]);
        $cogIp = new CognitoIdentityProvider('ap-southeast-2_2IsGtN0yq', [
            'version' => 'latest',
            'region'  => $this->awsConfig['s3']['region'],
            'scheme' => 'https',
            'credentials' => $this->cp
        ]);

        // End Remove


        $this->awsConfig = Yii::$app->params['aws'];

        // Credential provider
        $this->cp = CredentialProvider::ini('default', $this->awsConfig['credentialFilePath']);
        $this->cp = CredentialProvider::memoize($this->cp);

        // Create S3 access object
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->awsConfig['s3']['region'],
            'scheme' => 'https',
            'credentials' => $this->cp
        ]);


    }

    /**
     * Upload file to S3 bucket
     * @param string $fileName Destination file name
     * @param string $sourceFilePath Source file location
     * @param array $s3Options Any other s3 file uploading options
     * @return mixed true/false or s3 upload result
     */
    public function s3UploadObject($fileName, $sourceFilePath, $s3Options = [])
    {
        $result = null;

        $params = array_merge([
            'Bucket' => $this->awsConfig['s3']['bucketName'],
            'Key' => $fileName,
            'Body' => fopen($sourceFilePath, 'r+')
        ], $s3Options);

        try {
            $result = $this->s3->putObject($params);
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('S3 file upload failed.', ['error' => $e->getMessage()]);
        }

       return $result;
    }

    /**
     * Retrieve signed web accessible URL
     * @param string $fileName Destination file name
     * @param boolean $signed Whether signed URL or not
     * @param integer $expire Expire duration in minutes
     * @return string Object URL
     */
    public function s3GetObjectUrl($fileName, $signed = true, $expire = 300)
    {
        $url = null;
        try {
            if ($signed) {
                $cmd = $this->s3->getCommand('GetObject', [
                    'Bucket' => $this->awsConfig['s3']['bucketName'],
                    'Key' => $fileName,
                ]);
                $request = $this->s3->createPresignedRequest($cmd, "+{$expire} minutes");
                $url = (string)$request->getUri();
            } else {
                $url = $this->s3->getObjectUrl($this->awsConfig['s3']['bucketName'], $fileName);
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('S3 object URL retrieval failed.', ['error' => $e->getMessage()]);
        }

        return $url;
    }

    /**
     * Set object ACL
     * @param string $fileName Destination file name
     * @return string Object URL
     */
    public function s3PutObjectAcl($fileName)
    {
        $status = false;

        try {
            $res = $this->s3->putObjectAcl([
                'Bucket' => $this->awsConfig['s3']['bucketName'],
                'Key' => $fileName,
                'ACL' => 'public-read ',
            ]);

            if ('' != @$res->RequestCharged) {
                $status = true;
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog('S3 object ACL set failed.', ['error' => $e->getMessage()]);
        }

        return $status;
    }
}