<?php
namespace app\models;

use app\modules\api\components\ApiStatusMessages;
use app\models\BaseForm;

class UploadFile extends BaseForm
{
    // Compress file
    const COMPRESS_YES = 1;
    const COMPRESS_NO = 0;

    // Thumbnail
    const THUMBNAIL_YES = 1;
    const THUMBNAIL_NO =2;

    const SCENARIO_API_CREATE = 'apiCreate';
    const SCENARIO_API_FILE_URL = 'apiFileUrl';

    public $validImgFileTypes = ['image/jpeg', 'image/gif', 'image/png'];
    public $file;
    public $fileName;
    public $fileType;
    public $s3Options;
    public $options;
    public $signed;

    public function rules()
    {
        return [
            // Upload
            [['fileName'], 'required', 'message' => ApiStatusMessages::MISSING_MANDATORY_FIELD,
                'on' => [self::SCENARIO_API_CREATE, self::SCENARIO_API_FILE_URL]],
            [['s3Options', 'signed', 'options', 'fileType'], 'safe'],

            //[['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, txt, pdf'],
        ];
    }

    public function isImage($mimeType)
    {
        return in_array($mimeType, $this->validImgFileTypes);
    }

    /**
     * Check whether uploaded file type is correct for thumbnail generation
     *
     * @param string $imageName Image name
     * @param string $imagePath Image path
     * @return mixed $img Created image
     */
//    public function isValidFileForThumbCreate($fileName)
//    {
//        $name = $fileName;//$_FILES["file"]["name"];
//        $nameParts = explode(".", $name);
//        $ext = end($nameParts);
//
//        if (in_array($ext, $this->validImgFileTypes)) {
//            return true;
//        }
//
//        return false;
//    }
}
?>