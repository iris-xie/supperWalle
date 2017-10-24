<?php
/**
 * Created by PhpStorm.
 * User: Feron
 * Date: 2017/9/5
 * Time: 14:12
 */

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $configuration;

    public function rules ()
    {
        return [
            [['configuration'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip', 'maxSize' => 30485760, 'checkExtensionByMimeType' => false],
        ];
    }

    public function attributeLabels ()
    {
        return ['configuration' => '配置文件'];
    }

    public function upload ()
    {
        if ($this->validate()) {
            if ($this->configuration->saveAs(__DIR__.'/../web/upload/'.$this->configuration->baseName.'.'.$this->configuration->extension)) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->addError('configuration', $this->getErrors());
            return false;
        }
    }
}