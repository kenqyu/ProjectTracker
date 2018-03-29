<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $user \app\models\User
 */
use app\models\enums\UserRoles;
use app\models\enums\UserStatus;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Update job ' . $model->model->id;
$user = Yii::$app->user->identity;
\app\modules\jobs\assets\JobsUpdateAsset::register($this);
$this->registerMetaTag(['name' => 'job-id', 'content' => $model->model->id]);
$this->registerMetaTag(['name' => 'job-legacy-id', 'content' => $model->model->legacy_id]);
$this->registerJs("JobsUpdate.initMentions(" . json_encode(ArrayHelper::map(\app\models\User::find()->where(['status' => UserStatus::ACTIVE])->activated()->all(),
        "username", "fullName")) . ")");
?>
<div class="jobs update">
    <div class="modal_faker container">
        <div class="row">
            <div class="col-md-12">
                <div class="head">
                    <a href="/?list_view=<?= Yii::$app->request->get('list_view', 0) ?>"
                       class="btn btn-default pull-right" style="margin-left: 30px;"><i class="fa fa-times"></i></a>


                    <?php if ($model->model->jobGroup) { ?>
                        <div class="group"><i class="fa fa-chevron-right"></i> Part of <a
                                    href="<?= \yii\helpers\Url::to([
                                        'group',
                                        'id' => $model->model->job_group_id
                                    ]) ?>"><?= $model->model->jobGroup->name ?></a></div>
                    <?php } ?>
                    <h1><?= $model->legacy_id ?>: <?= $model->name ?></h1>
                    <div class="info">
                        <ul>

                            <li>
                                <strong>Requested by: </strong> <?= $model->model->creator->getShortName() ?>
                                (<?= $model->model->creator->email ?>)
                            </li>

                            <li>
                                <strong>Status: </strong> <?= \app\models\enums\JobStatus::getByValue($model->status)->text() ?>
                            </li>
                            <li>
                                <strong>Due: </strong> <?= date('m/d/Y', strtotime($model->model->due_date)) ?>
                            </li>

                            <?php if (!empty($model->model->old_id)) { ?>
                                <li>
                                    <a href="<?= \yii\helpers\Url::to([
                                        'archive/view-old',
                                        'id' => $model->model->old_id,
                                        'new' => $model->model->id
                                    ]) ?>"
                                    >Old Data</a>
                                </li>
                            <?php } ?>
                            <li>
                                <?php if ($user->subscribed($model->model)) { ?>
                                    <a href="<?= \yii\helpers\Url::to(['un-subscribe', 'id' => $model->model->id]) ?>"
                                    >Unsubscribe</a>
                                <?php } else {
                                    ?>
                                    <a href="<?= \yii\helpers\Url::to(['subscribe', 'id' => $model->model->id]) ?>"
                                    >Subscribe</a>
                                    <?php
                                } ?>
                            </li>

                        </ul>
                    </div>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'options' => [
                        'class' => 'form-vertical main_form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                echo \app\widgets\Alert::widget(['models' => $model]);
                echo \app\widgets\VerticalTabs::widget([
                    'options' => ['id' => 'update'],
                    'items' => [
                        [
                            'label' => 'Job info',
                            'content' => $this->render('partial/update/_job_info',
                                ['form' => $form, 'model' => $model]),
                            'active' => true
                        ],
                        [
                            'label' => 'Departmental fields',
                            'content' => $this->render('partial/update/_custom_fields',
                                ['form' => $form, 'model' => $model]),
                            'visible' => !empty($model->customFields)
                        ],
                        [
                            'label' => 'Assignment',
                            'content' => $this->render('partial/update/_assignment',
                                ['form' => $form, 'model' => $model])
                        ],
                        [
                            'label' => 'Accounting',
                            'content' => $this->render('partial/update/_accounting',
                                ['form' => $form, 'model' => $model]),
                            'visible' => $user->role >= UserRoles::MANAGER
                        ],
                        [
                            'label' => 'Translations',
                            'content' => $this->render('partial/update/_translations',
                                ['form' => $form, 'model' => $model]),
                            'visible' => $user->role >= UserRoles::MANAGER
                        ],
                        [
                            'label' => 'Close Out',
                            'content' => $this->render('partial/update/_close_out',
                                ['form' => $form, 'model' => $model]),
                            'visible' => $user->role >= UserRoles::MANAGER
                        ],
                        [
                            'label' => 'Comments/Attachments',
                            'content' => $this->render('partial/update/_comments',
                                ['form' => $form, 'model' => $model]),
                            'options' => ['id' => 'comments']
                        ],
                        [
                            'label' => 'Activity',
                            'content' => $this->render('partial/update/_history', ['form' => $form, 'model' => $model]),
                            'visible' => $user->role >= UserRoles::MANAGER,
                            'options' => ['id' => 'activity']
                        ],
                    ]
                ]);
                ActiveForm::end();
                ?>
            </div>
        </div>
    </div>
</div>
