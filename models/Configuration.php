<?php
/**
 * Created by PhpStorm.
 * User: Feron
 * Date: 2017/9/5
 * Time: 11:16
 */

namespace app\models;

use yii\db\ActiveRecord;

class Configuration extends ActiveRecord
{
    public static function tableName ()
    {
        return 'project_configuration';
    }

    public function rules ()
    {
        return [[['project_id', 'user_id', 'username', 'remark'], 'required'],
                [['project_id', 'user_id'], 'integer'],
                [['created_at', 'updated_at'], 'safe'],
                [['username', 'remark'], 'string'],
        ];
    }

    public function attributeLabels ()
    {
        return ['project_id' => '项目',
                'user_id'    => '用户id',
                'username'   => '用户名',
                'remark'     => '备注',
        ];
    }

    public static function getNewestConfig($task)
    {
        $config = Configuration::find()
            ->select(['upload_path','file_name'])
            ->where(['project_id' => $task])
            ->orderBy([
                'updated_at' => SORT_DESC,
            ])
            ->one();
        file_put_contents('/tmp/xielei.txt',print_r($config,true)."55555\n",FILE_APPEND);

        return strstr($config['upload_path'].$config['file_name'], '.zip',true);
    }
}