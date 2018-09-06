<?php

namespace app\commands;

use app\models\Property;
use Yii;
use yii\console\Controller;
use app\models\User;
use app\components\Aws;

/*
 * This command contains various utility actions
 */

class UtilController extends Controller
{
    public $user;
    public $aws;
    public $property;

    public function init()
    {
        Yii::$app->appLog->action = __CLASS__;
        Yii::$app->appLog->uniqid = uniqid();
        Yii::$app->appLog->logType = 3;

        $this->aws = new Aws();

        parent::init();
    }

    /**
     * Make existing profile pictures public
     */
    public function actionMakePublic()
    {
        Yii::$app->appLog->writeLog('Start');

        $this->user = new User();

        $page = 1;

        do {
            $users = $this->user->getUsers($page);
            if (!empty($users)) {
                foreach ($users as $user) {
                    Yii::$app->appLog->uniqid = uniqid();
                    Yii::$app->appLog->writeLog('Changing object ACL.', ['userId' => $user->id,
                        'profileImage' => $user->profileImage, 'profileImageThumb' => $user->profileImageThumb]);

                    if (!stristr($user->profileImage, 'http') && '' != $user->profileImage) {
                        $this->aws->s3PutObjectAcl($user->profileImage);
                    }

                    if (!stristr($user->profileImageThumb, 'http') && '' != $user->profileImageThumb) {
                        $this->aws->s3PutObjectAcl($user->profileImageThumb);
                    }
                }
            } else {
                Yii::$app->appLog->writeLog('No more users');
            }
            $page++;
        } while (!empty($users));

        Yii::$app->appLog->writeLog('Stop');
    }

    /**
     * Re arrange existing property images to new format.
     */
    public function actionArrangePropertyImages()
    {
        Yii::$app->appLog->writeLog('Start');

        $this->property = new Property();

        $page = 1;

        do {
            $properties = $this->property->getProperties($page);
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    $images = [];
                    Yii::$app->appLog->uniqid = uniqid();
                    $curImages = json_decode($property->images, true);
                    if (empty($curImages)) {
                        if ($property->imageName != '') {
                            $this->aws->s3PutObjectAcl($property->imageName);
                            $this->aws->s3PutObjectAcl($property->thumbImageName);
                            $images[] = [
                                'imageName' => $property->imageName,
                                'thumbImageName' => $property->thumbImageName,
                                'isDefault' => 1,
                            ];
                        }
                    } else {
                        foreach ($curImages as $curImage) {
                            if (null != $curImage['imageName']) {
                                $this->aws->s3PutObjectAcl($curImage['imageName']);
                            }

                            if (null != $curImage['thumbImageName']) {
                                $this->aws->s3PutObjectAcl($curImage['thumbImageName']);
                            }

                            if (null != $curImage['imageName']) {
                                $images[] = $curImage;
                            }
                        }
                    }

                    $property->images = json_encode($images);
                    $property->saveModel();
                }
            } else {
                Yii::$app->appLog->writeLog('No more properties');
            }
            $page++;
        } while (!empty($properties));

        Yii::$app->appLog->writeLog('Stop');
    }
}
