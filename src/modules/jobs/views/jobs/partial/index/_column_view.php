<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\modules\jobs\models\JobSearchForm
 */
use app\models\enums\UserRoles;

?>
<div class="col-md-12">
    <div class="grid">
        <?php foreach (collect(\app\models\enums\JobStatus::getData())->sortBy('order')->all() as $key => $item) {
            ?>

            <div class="column"
                 style="<?= in_array($key, [4, 5, 6, 8]) ? 'display:none' : '' ?>"
                 data-column="<?= $key ?>">
                <div class="jobs-status-container" data-status="<?= $key ?>">
                    <h2><?= $item['text'] ?></h2>

                    <div class="items">
                        <?php
                        $q = \app\models\Job::find()
                            ->with('creator')
                            ->orderBy(['id' => SORT_DESC]);
                        if (Yii::$app->user->identity->role >= UserRoles::GENERAL) {
                            if (Yii::$app->session->get('all_projects', 0) == 0
                            ) {
                                $q->orWhere(['creator_id' => Yii::$app->user->id]);
                                $q->orWhere(['project_lead_id' => Yii::$app->user->id]);
                                $q->orWhere(['project_manager_id' => Yii::$app->user->id]);
                                $q->orWhere(['iwcm_publishing_assignee_id' => Yii::$app->user->id]);
                                $q->orWhere(['ccc_contact_id' => Yii::$app->user->id]);
                            } else {
                                $q->andWhere(['processing_unit_id' => Yii::$app->user->identity->getProcessingUnitsIds()]);
                            }
                        } elseif (Yii::$app->user->identity->role == UserRoles::GENERAL) {
                            $q->orWhere(['creator_id' => Yii::$app->user->id]);
                        }

                        $q->andWhere([
                            'status' => $key
                        ]);
                        if ($key == \app\models\enums\JobStatus::COMPLETED || $key == \app\models\enums\JobStatus::CANCELED) {
                            $q->andWhere([
                                '>=',
                                'updated_at',
                                new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 30 day)')
                            ]);
                        }
                        foreach ($q->all() as $job) {
                            echo $this->render('__job_list_item',
                                [
                                    'model' => $job,
                                    'createForm' => isset($model) ? $model : null
                                ]);
                        } ?>
                    </div>
                </div>
            </div>

            <?php
        } ?>
    </div>
</div>
