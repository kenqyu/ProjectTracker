<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
?>

<div class="row comments">
    <div class="col-md-12">

        <div class="add">
            <div class="help-block">
                You can mention people in comments by using "@" followed by the first or last name, then selecting the
                people you would like to mention from the list. Mentioned user(s) will get a notification about the
                comment in two ways: through the Notification Panel, and via email. You can mention any user who is
                registered with the system to collaborate on projects. The user does not have to be assigned to the
                project to receive notifications.
            </div>
            <textarea name="comment" id="new_comment" rows="5" class="form-control"
                      placeholder='Type "@" followed by the recipient’s first or last name to exchange communications about your request'></textarea>
            <div class="row">
                <div class="col-md-6">
                    <a href="#" class="btn btn-primary btn-lg" id="add_comment">Post</a>
                </div>
                <div class="col-md-6 text-right">
                    <span class="btn btn-primary btn-lg btn-block fileinput-button">
        <span>Upload Attachment</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <?= \yii\bootstrap\Html::fileInput('file', null, ['id' => 'fileupload']) ?>
    </span>
                    <div class="text-left warning">Warning! Do not upload any files containing sensitive information.
                        <br>Use SharePoint and provide a link.
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="list">
            <?= $this->render('__comments_list', ['model' => $model->model]); ?>
        </div>
    </div>
</div>
