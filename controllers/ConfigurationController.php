<?php
/**
 * Created by PhpStorm.
 * User: Feron
 * Date: 2017/8/29
 * Time: 16:43
 */

namespace app\controllers;

use app\components\Controller;
use app\models\UploadForm;
use app\models\User;
use app\models\Group;
use app\models\Project;
use app\models\Configuration;
use yii\web\UploadedFile;
use yii;

class ConfigurationController extends Controller
{
    const UPLOAD_PATH = __DIR__.'/../web/upload/';
    public function actionManage ()
    {
        $select        = [];
        $user          = User::findOne(['id' => $this->uid]);
        $group_table   = Group::tableName();
        $project_table = Project::tableName();
        $projects      = Project::find()
                                ->leftJoin($group_table, "`$group_table`.`project_id` = `$project_table`.`id`")
                                ->where(["`$project_table`.status"  => Project::STATUS_VALID,
                                         "`$group_table`.`user_id`" => $this->uid
                                        ])
                                ->asArray()
                                ->all();
        foreach ($projects as $index => $project) {
            $select[(int) $projects[$index]['id']] = $projects[$index]['name'];
        }
        $select[0]     = '项目名称';
        $configuration = new Configuration();
        $upload        = new UploadForm();
        if (\Yii::$app->request->isPost) {
            $upload->configuration = UploadedFile::getInstance($upload, 'configuration');
            $is_upload             = $upload->upload();
            if ($is_upload) {
                if ($configuration->load(\Yii::$app->request->post()) && $configuration->validate()) {
                    $filename                   = $upload->configuration->baseName.'.'.$upload->configuration->extension;
                    $configuration->upload_path = self::UPLOAD_PATH;
                    $configuration->file_name   = $filename;
                    if ($configuration->save(false)) {
                        Yii::$app->session->setFlash('success', Yii::t('configuration', 'message success'));
                        $zip = new \ZipArchive();
                        if(true === $zip->open(self::UPLOAD_PATH.'/'.$filename)){
                            $zip->extractTo(self::UPLOAD_PATH);
                            $zip->close();
                        }
                    }
                }
            } else {
                Yii::$app->session->setFlash('failure', Yii::t('configuration', 'message failure'));
            }

        }

        return $this->render('manager', ['user'          => $user,
                                         'select'        => $select,
                                         'configuration' => $configuration,
                                         'upload'        => $upload
        ]);
    }
}