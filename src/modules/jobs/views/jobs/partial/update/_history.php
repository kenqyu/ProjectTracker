<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\modules\jobs\models\UpdateJobForm
 * @var $form \yii\bootstrap\ActiveForm
 */
?>
<div class="row activity">
    <div class="col-md-12">


        <div class="row">
            <div class="col-md-12">
                <div class="row" style="font-weight: bold;margin-bottom: 20px;">
                    <div class="col-md-3">Modified Fields</div>
                    <div class="col-md-4">Before</div>
                    <div class="col-md-5">After</div>
                </div>
                <?php
                $i = 0;
                foreach (\app\models\JobLog::find()->where(['job_id' => $model->model->id])->joinWith([
                    'user'
                ])->orderBy(['id' => SORT_DESC])->all() as $item) {
                    /**
                     * @var $item \app\models\JobLog
                     */
                    $tmp = $item->getPrev();
                    $after = json_decode($item->after, true);
                    if ($tmp) {
                        $prev = json_decode($item->getPrev()->after, true);
                    } else {
                        $prev = [];
                    }

                    $changes = $item->getChanges($prev);

                    $relations = [
                        'workTypes',
                        'jobCostCenters',
                        'jobFiles',
                        'jobInvoices',
                        'jobTranslations',
                        'cccContact',
                        'iwcmPublishingAssignee',
                        'projectLead',
                        'projectManager',
                        'translationManager',
                        'creator',
                        'jobLinks',
                        'department',
                        'processingUnit'
                    ];
                    $ignore = [
                        'department_id',
                        'processing_unit_id',
                        'creator_id',
                        'project_lead_id',
                        'project_manager_id',
                        'translation_manager_id',
                        'agency_id',
                        'iwcm_publishing_assignee_id',
                        'ccc_contact_id',
                        'created_at',
                        'updated_at',
                        'cwa_id'
                    ];

                    $diff = [];

                    foreach ($after as $k => $v) {
                        if (!in_array($k, $ignore)) {
                            if (!in_array($k, $relations)) {
                                if (isset($prev[$k]) && isset($after[$k])) {
                                    if ($prev[$k] != $after[$k]) {
                                        $diff[$k] = $v;
                                    }
                                } else {
                                    $diff[$k] = $v;
                                }
                            } else {
                            }
                        }
                    }


                    foreach ($ignore as $key) {
                        unset($changes[$key]);
                    }
                    $changes = collect($changes)->filter(function ($item) {
                        return !empty($item);
                    })->all();
                    if (empty($changes)) {
                        continue;
                    }
                    $i++;
                    ?>
                    <div class="item col-md-12">
                        <div class="meta">
                            <?= $item->user->getShortName() ?> • <?= Yii::$app->formatter->asDatetime($item->created_at,
                                'short') ?>
                        </div>
                        <div class="changes">
                            <?php foreach ($changes as $key => $value) {
                                echo '<div class="row">';

                                echo '<div class="col-md-3">';
                                echo $item->getAttributeLabel($key);
                                echo '</div>';

                                echo '<div class="col-md-4">';
                                if (isset($prev[$key])) {
                                    if (in_array($key, $relations)) {
                                        if (in_array($key, [
                                            'jobCostCenters',
                                            'jobInvoices',
                                            'jobFiles',
                                            //'workTypes',
                                            'jobTranslations',
                                            'jobLinks'
                                        ])) {
                                            foreach ($prev[$key] as $k2 => $i2) {
                                                if (isset($prev[$key][$k2]) && (!isset($after[$key][$k2]) || $prev[$key][$k2] != $after[$key][$k2])) {
                                                    switch ($key) {
                                                        case 'jobCostCenters':
                                                            echo $prev[$key][$k2]['name'] . ' - ' . $prev[$key][$k2]['percent'] . '%<br>';
                                                            break;
                                                        case 'jobInvoices':
                                                            echo $prev[$key][$k2]['number'] . ' - $' . $prev[$key][$k2]['amount'] . '<br>';
                                                            break;
                                                        case 'jobFiles':
                                                        case 'workTypes':
                                                            echo $prev[$key][$k2]['name'] . '<br>';
                                                            break;
                                                        case 'jobLinks':
                                                            echo \yii\bootstrap\Html::a($prev[$key][$k2]['link'],
                                                                    $prev[$key][$k2]['link'],
                                                                    ['target' => '_blank']) . '<br>';
                                                            break;
                                                        case 'jobTranslations':
                                                            echo \app\models\enums\Languages::getByValue($prev[$key][$k2]['language'])->text() . ' - ' . \app\models\enums\JobTranslationStatus::getByValue($prev[$key][$k2]['status'])->text() . '<br>';
                                                            break;
                                                    }
                                                }
                                            }
                                        } elseif ($key == 'workTypes' && (!isset($after[$key]) || $after[$key] != $prev[$key])) {
                                            foreach ($prev[$key] as $wt) {
                                                echo $wt['name'] . '<br>';
                                            }
                                        } else {
                                            if (isset($prev[$key])) {
                                                echo $prev[$key]['username'];
                                            }
                                        }
                                    } else {
                                        if (isset($prev[$key])) {
                                            if (in_array($key, [
                                                'translation_needed',
                                                'mandate',
                                                'internal_only',
                                                'ccc_impact',
                                                'one_voice'
                                            ])) {
                                                echo $prev[$key] == 1 ? 'Yes' : 'No';
                                            } elseif ($key == 'description') {
                                                echo \yii\bootstrap\Html::tag('div', nl2br($prev[$key]),
                                                    ['style' => 'overflow-x:auto; max-width: 100%; white-space:nowrap;']);
                                            } else {
                                                if ($key == 'status') {
                                                    echo \app\models\enums\JobStatus::getByValue($prev[$key])->text();
                                                } else {
                                                    echo $prev[$key];
                                                }
                                            }
                                        }
                                    }
                                }
                                echo '</div>';

                                echo '<div style="border-left:1px solid #ccc;" class="col-md-5">';
                                if (in_array($key, $relations)) {
                                    if (in_array($key, [
                                        'jobCostCenters',
                                        'jobInvoices',
                                        'jobFiles',
                                        //'workTypes',
                                        'jobTranslations',
                                        'jobLinks'
                                    ])) {
                                        if (isset($after[$key])) {
                                            foreach ($after[$key] as $k2 => $i2) {
                                                if (isset($after[$key][$k2]) && (!isset($prev[$key][$k2]) || $after[$key][$k2] != $prev[$key][$k2])) {
                                                    switch ($key) {
                                                        case 'jobCostCenters':
                                                            echo $after[$key][$k2]['name'] . ' - ' . $after[$key][$k2]['percent'] . '%<br>';
                                                            break;
                                                        case 'jobInvoices':
                                                            echo $after[$key][$k2]['number'] . ' - $' . $after[$key][$k2]['amount'] . '<br>';
                                                            break;
                                                        case 'jobFiles':
                                                        case 'workTypes':
                                                            echo $after[$key][$k2]['name'] . '<br>';
                                                            break;
                                                        case 'jobLinks':
                                                            echo \yii\bootstrap\Html::a(
                                                                    $after[$key][$k2]['link'],
                                                                    $after[$key][$k2]['link'],
                                                                    ['target' => '_blank']) . '<br>';
                                                            break;
                                                        case 'jobTranslations':
                                                            echo \app\models\enums\Languages::getByValue($after[$key][$k2]['language'])->text() . ' - ' . \app\models\enums\JobTranslationStatus::getByValue($after[$key][$k2]['status'])->text() . '<br>';
                                                            break;
                                                    }
                                                    break;
                                                }
                                            }
                                        }
                                    } elseif ($key == 'workTypes' && (!isset($prev[$key]) || $after[$key] != $prev[$key])) {
                                        foreach ($after[$key] as $wt) {
                                            echo $wt['name'] . '<br>';;
                                        }
                                    } else {
                                        if (isset($after[$key])) {
                                            echo $after[$key]['username'];
                                        }
                                    }
                                } else {
                                    if (isset($after[$key])) {
                                        if (in_array($key, [
                                            'translation_needed',
                                            'mandate',
                                            'internal_only',
                                            'ccc_impact',
                                            'one_voice'
                                        ])) {
                                            echo $after[$key] == 1 ? 'Yes' : 'No';
                                        } elseif ($key == 'description') {
                                            echo \yii\bootstrap\Html::tag('div', nl2br($after[$key]),
                                                ['style' => 'overflow-x:auto; max-width: 100%; white-space:nowrap;']);
                                        } else {
                                            if ($key == 'status') {
                                                echo \app\models\enums\JobStatus::getByValue($after[$key])->text();
                                            } else {
                                                echo $after[$key];
                                            }
                                        }
                                    }
                                }
                                echo '</div>';

                                echo '</div>';
                            } ?>
                        </div>
                    </div>
                <?php }
                if ($i == 0) {
                    ?>
                    <h3 class="text-center">No activity found</h3>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
