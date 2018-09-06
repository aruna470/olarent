<?php

namespace app\models;

use Yii;
use app\models\Base;

/**
 * This is the model class for table "NotificationQueue".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $status
 * @property string $data
 * @property string $createdAt
 */
class NotificationQueue extends Base
{
    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;

    const TYPE_ASSIGN_ANOTHER = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'NotificationQueue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'data', 'createdAt'], 'required'],
            [['type', 'status'], 'integer'],
            [['data'], 'string'],
            [['createdAt'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'data' => Yii::t('app', 'Data'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Add to queue
     */
    public function addQueue($type, $data)
    {
        $model = new NotificationQueue();
        $model->type = $type;
        $model->status = self::STATUS_PENDING;
        $model->data = $data;

        return $model->saveModel();
    }

    /**
     * Update queue
     */
    public function updateQueue($id, $status)
    {
        $model = NotificationQueue::findOne($id);
        $model->status = $status;

        return $model->saveModel();
    }

    /**
     * Retrieve pending queues
     */
    public function getPendingQueueList()
    {
        return NotificationQueue::find()
            ->andWhere('status = :status', [':status' => self::STATUS_PENDING])
            ->all();
    }
}
