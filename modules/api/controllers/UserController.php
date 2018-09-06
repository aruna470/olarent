<?php

namespace app\modules\api\controllers;

class UserController extends ApiBaseController
{
    public function actions(){
        return array(
            'create' => 'app\modules\api\controllers\actions\user\Create',
            'index' => 'app\modules\api\controllers\actions\user\Index',
            'view' => 'app\modules\api\controllers\actions\user\View',
            'update' => 'app\modules\api\controllers\actions\user\Update',
            'delete' => 'app\modules\api\controllers\actions\user\Delete',
            'authenticate' => 'app\modules\api\controllers\actions\user\Authenticate',
            'change-password' => 'app\modules\api\controllers\actions\user\ChangePassword',
            'invite-tenant' => 'app\modules\api\controllers\actions\user\InviteTenant',
            'my-owners' => 'app\modules\api\controllers\actions\user\MyOwner',
            'send-verify-code' => 'app\modules\api\controllers\actions\user\SendVerifyCode',
            'verify-code' => 'app\modules\api\controllers\actions\user\VerifyCode',
            'forgot-password' => 'app\modules\api\controllers\actions\user\ForgotPassword',
            'reset-password' => 'app\modules\api\controllers\actions\user\ResetPassword',
        );
    }
}
