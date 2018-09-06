<?php

namespace app\models;

use Yii;
use yii\base\Model;

class BaseForm extends Model
{
    public $statusCode = null;
    public $statusMessage = null;

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
}
