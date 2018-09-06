<?php

namespace app\modules\api\controllers;

class UserMpInfoController extends ApiBaseController
{
    public function actions(){
        return array(
            'create' => 'app\modules\api\controllers\actions\userMpInfo\Create',
            'update' => 'app\modules\api\controllers\actions\userMpInfo\Update',
            'view' => 'app\modules\api\controllers\actions\userMpInfo\View',
            'get-mp-form-info' => 'app\modules\api\controllers\actions\userMpInfo\GetMpFormInfo',
            'create-file' => 'app\modules\api\controllers\actions\userMpInfo\CreateFile',
            'get-files' => 'app\modules\api\controllers\actions\userMpInfo\GetFiles',
        );
    }
}
