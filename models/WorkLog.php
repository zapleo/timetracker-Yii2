<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_log".
 *
 * @property integer $id
 * @property integer $activityIndex
 * @property integer $countMouseEvent
 * @property integer $countKeyboardEvent
 * @property string $screenshot
 * @property string $dateTime
 * @property integer $user_id
 * @property string $issueKey
 * @property integer $workTime
 *
 * @property Users $user
 */
class WorkLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activityIndex', 'countMouseEvent', 'countKeyboardEvent', 'screenshot', 'user_id', 'issueKey'], 'required'],
            [['activityIndex', 'countMouseEvent', 'countKeyboardEvent', 'user_id', 'workTime'], 'integer'],
            [['dateTime'], 'safe'],
            [['screenshot'], 'string', 'max' => 255],
            [['issueKey'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activityIndex' => 'Activity Index',
            'countMouseEvent' => 'Count Mouse Event',
            'countKeyboardEvent' => 'Count Keyboard Event',
            'screenshot' => 'Screenshot',
            'dateTime' => 'Date Time',
            'user_id' => 'User ID',
            'issueKey' => 'Issue Key',
            'workTime' => 'Work Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
