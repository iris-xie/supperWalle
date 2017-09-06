<?php
/**
 * Created by PhpStorm.
 * User: Feron
 * Date: 2017/8/30
 * Time: 9:41
 */
$this->title = yii::t('configuration', 'manage title');
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Alert;
?>
<div class="box">
    <div class="box-header">
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding clearfix">
        <?php $form = ActiveForm::begin(['id' => 'configuration-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <table class="table table-striped table-bordered table-hover">
                <tbody>
                <tr>
                    <th><?= yii::t('configuration', 'file name') ?></th>
                    <th><?= yii::t('configuration', 'user') ?></th>
                    <th><?= yii::t('configuration', 'project') ?></th>
                    <th><?= yii::t('configuration', 'remark') ?></th>
                </tr>
                <?php if(!empty($details)) { ?>
                    <?php foreach ($details as $detail) {?>
                    <tr>
                        <td>
                            <?= $detail['file_name'];?>
                        </td>
                        <td>
                            <?= $detail['username'];?>
                        </td>
                        <td>
                            <?= $detail['project_name'];?>
                        </td>
                        <td>
                            <?= $detail['remark']; ?>
                        </td>
                    </tr>
                    <?php }?>
                <?php } ?>
                <tr>
                    <td>
                        <div>
                            <?= $form->field($upload, 'configuration')->fileInput()->label(false); ?>
                        </div>
                    </td>
                    <td>
                        <?= $user['name'];?>
                        <?= $form->field($configuration, 'user_id')->hiddenInput(['value'=> $user['id']])->label(false); ?>
                        <?= $form->field($configuration, 'username')->hiddenInput(['value'=> $user['name']])->label(false); ?>
                    </td>
                    <td>
                        <?= $form->field($configuration, 'project_id')->dropDownList($select,['prompt' => yii::t('configuration', 'list project')])->label(false)?>
                    </td>
                    <td>
                        <?= $form->field($configuration, 'remark')->textInput()->label(false);?>
                    </td>
                </tr>
                </tbody>
            </table>
            <span class="input-group-btn">
                <?= Html::submitButton(yii::t('configuration', 'button submit'), ['class' => 'btn btn-primary btn-purple', 'style' => 'float:right']) ?>
            </span>
            <?php ActiveForm::end();?>
    </div><!-- /.box-body -->
    <?php
    if( Yii::$app->getSession()->hasFlash('success') ) {
        echo Alert::widget([
                               'options' => [
                                   'class' => 'alert-block alert-success', //这里是提示框的class
                                   'style' => 'width:400px;background:#4cae4c;text-align:center;margin:0 auto;border-radius:5px;'
                               ],
                               'body' => Yii::$app->getSession()->getFlash('success'), //消息体
                           ]);
    }
    if( Yii::$app->getSession()->hasFlash('failure') ) {
        echo Alert::widget([
                               'options' => [
                                   'class' => 'alert-block alert-error',
                                   'style' => 'width:400px;background:#4cae4c;text-align:center;margin:0 auto;border-radius:5px;'
                               ],
                               'body' => Yii::$app->getSession()->getFlash('failure'),
                           ]);
    }
    ?>
</div>
