<?php

namespace app\modules\api\controllers\actions\userMpInfo;

use Yii;
use yii\base\Action;
use app\models\UserMpInfoFile;
use app\modules\api\components\Messages;

class GetFiles extends Action
{
    public function run()
    {
        $user = $this->controller->user;
        $userMpInfoFile = new UserMpInfoFile();

        $fileList = [];
        $userMpInfoFile->userId = $user->id;
        $files = $userMpInfoFile->getFiles();

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileList[] = Messages::userMpInfoFile($file);
            }
        }

        $this->controller->sendResponse($fileList);
    }
}
?>