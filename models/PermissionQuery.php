<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Permission]].
 *
 * @see Permission
 */
class PermissionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Permission[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Permission|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}