<?php

namespace app\modules\api\controllers\actions\util;


use Yii;
use yii\base\Action;
use app\models\AdyenNotification;

class AdyenNoti extends Action
{
    public function run()
    {
        $model = new AdyenNotification();
        $model->attributes = Yii::$app->request->post();
        $model->saveModel();

        print "[accepted]";
    }
}
?>