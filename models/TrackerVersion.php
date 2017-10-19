<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tracker_version".
 *
 * @property integer $id
 * @property string $version
 * @property string $date
 */
class TrackerVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tracker_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version'], 'required'],
            [['date'], 'safe'],
            [['version'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'date' => 'Date',
        ];
    }
}
