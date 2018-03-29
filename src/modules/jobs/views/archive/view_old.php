<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\OldJob
 */
$this->title = 'Old Job ' . $model->name;
$user = Yii::$app->user->identity;
?>
<div class="old_job">
    <div class="modal_faker container">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <?php
                    if (Yii::$app->request->get('new')) {
                        ?>
                        <a href="<?= \yii\helpers\Url::to(['jobs/update', 'id' => Yii::$app->request->get('new')]) ?>"
                           class="btn btn-default">Back new item</a>
                        <?php
                    } else {
                        ?>
                        <a href="<?= \yii\helpers\Url::to(['old-index']) ?>" class="btn btn-default">Back to list</a>
                        <?php
                    }
                    ?>
                </p>
                <?= \yii\widgets\DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'number',
                        'name',
                        'description',
                        'submitted_by',
                        'submit_date',
                        'rush:boolean',
                        'due_date',
                        'work_type',
                        'justifications',
                        'dce_lead',
                        'status',
                        'last_date_user',
                        'last_update_date',
                        'comments',
                        'it_notification',
                        'iwcm_publishing_assignee',
                        'complete_date',
                        'current_url',
                        'ccc_impact:boolean',
                        'ccc_contact',
                        'affiliate_compliance',
                        'imcli:boolean',
                        'related_olm',
                        'sce_approvers',
                        'accounting',
                        'cwa',
                        'estimate_amount',
                        'translation_needed:boolean',
                        'translation_rush:boolean',
                        'translation_request_date',
                        'translation_due_date',
                        'translation_status',
                        'attachment',
                        'invoice_number',
                        'invoice_amount',
                        'publishing_date',
                        'requestor_email',
                        'project_url',
                        'progress',
                        'size'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>

