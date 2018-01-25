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
 * @property string $screenshot_preview
 * @property string $dateTime
 * @property integer $user_id
 * @property string $issueKey
 * @property integer $workTime
 * @property integer $timestamp
 * @property string $comment
 * @property string $manual_time_id
 * @property integer $work_log_id
 *
 * @property User $user
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
            [['activityIndex', 'countMouseEvent', 'countKeyboardEvent', 'user_id', 'workTime', 'timestamp', 'manual_time_id', 'work_log_id'], 'integer'],
            [['dateTime'], 'safe'],
            [['screenshot', 'screenshot_preview'], 'string', 'max' => 255],
            [['screenshot'], 'unique'],
            [['issueKey'], 'string', 'max' => 10],
            [['comment'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'screenshot_preview' => 'Screenshot preview',
            'dateTime' => 'Date Time',
            'user_id' => 'User ID',
            'issueKey' => 'Issue Key',
            'workTime' => 'Work Time',
            'timestamp' => 'Time',
            'comment' => 'Comment',
            'manual_time_id' => 'Manual time',
            'work_log_id' => 'Jira work log'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
