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
        return [[['project_id', 'user_id', 'username', 'origin_path', 'remark'], 'required'],
                [['project_id', 'user_id'], 'integer'],
                [['created_at', 'updated_at'], 'safe'],
                [['username', 'origin_path', 'remark'], 'string'],
                ['origin_path', 'match', 'pattern' => '/^([\w]+[\/]?)+$/',]

        ];
    }

    public function attributeLabels ()
    {
        return ['origin_path' => '文件路径',
                'project_id'  => '项目',
                'user_id'     => '用户id',
                'username'    => '用户名',
                'remark'      => '备注',
        ];
    }
}