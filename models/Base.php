<?php

namespace app\models;

use Yii;

class Base extends \yii\db\ActiveRecord
{
    public $statusCode = null;
    public $statusMessage = null;
    public $errorCode = null;

    public $log = null;

    private $_oldAttributes = array();

    const STATUS_DEACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const CRYPT_SALT = '$6$rounds=5000$V%7^CFF73;8^h~E$';

    private $_statuses = [
        self::STATUS_DEACTIVE => 'Deactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    public function init()
    {
        $this->log = Yii::$app->appLog;
        $this->loadDefaultValues();
        parent::init();
    }

    public function getOldAttributes()
    {
        return $this->_oldAttributes;
    }

    public function setOldAttributes($value)
    {
        $this->_oldAttributes = $value;
    }

    /**
     * Returns the statuses.
     * @return array statuses array.
     */
    public function getStatuses()
    {
        return $this->_statuses;
    }

    public function beforeValidate()
    {
        // updatedAt
        if ($this->hasAttribute('updatedAt'))
            $this->updatedAt = Yii::$app->util->getUtcDateTime();

        // updatedById
        if ($this->hasAttribute('updatedById') && null == $this->updatedById) {
            if (Yii::$app instanceof \yii\web\Application && is_object(Yii::$app->user->identity)) {
                $this->updatedById = Yii::$app->user->identity->id;
            } elseif (Yii::$app instanceof \yii\console\Application) {
                $this->updatedById = -1;
            } else {
                $this->updatedById = null;
            }
        }

        // New record
        if ($this->isNewRecord) {
            // createdAt
            if ($this->hasAttribute('createdAt'))
                $this->createdAt = Yii::$app->util->getUtcDateTime();

            // createdById
            if ($this->hasAttribute('createdById') && null == $this->createdById) {
                if (Yii::$app instanceof \yii\web\Application && is_object(Yii::$app->user->identity)) {
                    $this->createdById = Yii::$app->user->identity->id;
                } elseif (Yii::$app instanceof \yii\console\Application) {
                    $this->createdById = -1;
                } else {
                    $this->createdById = null;
                }
            }

            // updatedAt
            if ($this->hasAttribute('updatedAt'))
                $this->updatedAt = null;

            // updatedById
            if ($this->hasAttribute('updatedById'))
                $this->updatedById = null;
        }
        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        $attrs = $this->getAttributes();

        foreach ($attrs as $name => $value) {
            $this->$name = trim($value);

            if ($this->$name === '')
                $this->$name = null;
        }
        return parent::afterValidate();
    }

    /**
     * Trim model attributes
     */
    public function trimAttributes()
    {
        $attrs = $this->getAttributes();

        foreach ($attrs as $name => $value) {
            $this->$name = trim($value);
        }
    }

    public function getLastError()
    {
        $errorData = [];
        if ($this->hasErrors()) {
            $errors = $this->getFirstErrors();
            reset($errors);
            list($attribute, $message) = each($errors);
            $errorData = [
                'attribute' => $attribute,
                'message' => $message
            ];
        }

        return $errorData;
    }

    public function afterFind()
    {
        $this->setOldAttributes($this->getAttributes());
    }

    /**
     * Save multiple models
     * @param mixed $models
     * @return boolean true of false.
     */
    public function saveMultiple($models)
    {
        $allSuc = true;
        foreach ($models as $model) {
            if (!$model->saveModel()) {
                $allSuc = false;
            }
        }

        return $allSuc;
    }

    /**
     * Generic function to save any model
     * @return boolean $status true of false.
     */
    public function saveModel()
    {
        $name = get_class($this);
        $status = false;
        if ($this->validate()) {
            try {
                if ($this->save()) {
                    $status = true;
                    Yii::$app->appLog->writeLog("{$name} record saved.", ['attributes' => $this->attributes]);
                } else {
                    Yii::$app->appLog->writeLog("{$name} record save failed.", ['errors' => $this->errors, 'attributes' => $this->attributes]);
                }
            } catch (\Exception $e) {
                Yii::$app->appLog->writeLog("{$name} record save failed", ['exception' => $e->getMessage(), 'attributes' => $this->attributes]);
            }
        } else {
            Yii::$app->appLog->writeLog("{$name} record save failed. Validation errors.", ['errors' => $this->errors, 'attributes' => $this->attributes]);

            // Use for API validations
            $errors = $this->getLastError();
            if (!empty($errors)) {
                $this->statusCode = $errors['message'];
                $this->statusMessage = $errors['attribute'];
            }
        }

        return $status;
    }

    /**
     * Validate specific model
     * @param array $attributes specific attributes to be validated
     * @return boolean $status true of false.
     */
    public function validateModel($attributes = null)
    {
        $name = get_class($this);

        if ($this->validate($attributes)) {
            Yii::$app->appLog->writeLog("{$name} record validate success. Validation errors.");
            return true;
        } else {
            Yii::$app->appLog->writeLog("{$name} Record validate failed. Validation errors.", ['errors' => $this->errors, 'attributes' => $this->attributes]);
            // Use for API validations
            $errors = $this->getLastError();
            if (!empty($errors)) {
                $this->statusCode = $errors['message'];
                $this->statusMessage = $errors['attribute'];
            }
            return false;
        }
    }

    /**
     * Generic function to delete any model
     * @return boolean $status true of false.
     */
    public function deleteModel()
    {
        $name = get_class($this);
        $status = false;

        try {
            if ($this->delete()) {
                $status = true;
                Yii::$app->appLog->writeLog("{$name} record deleted.", ['attributes' => $this->attributes]);
            } else {
                Yii::$app->appLog->writeLog("{$name} Record delete failed.", ['attributes' => $this->attributes]);
            }
        } catch (\Exception $e) {
            Yii::$app->appLog->writeLog("{$name} Record delete failed.", ['exception' => $e->getMessage(), 'attributes' => $this->attributes]);
        }

        return $status;
    }
}
