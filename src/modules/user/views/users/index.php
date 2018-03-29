<?php
/**
 * @var $this \yii\web\View
 */
$this->title = 'Users';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?= \app\widgets\Alert::widget() ?>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'email',
                    [
                        'attribute' => 'organization_unit',
                        'label' => 'Organization Unit',
                        'filter' => \app\models\OrganizationUnit::getDataList(true, -1, '(not set)'),
                        'format' => 'html',
                        'value' => function ($item) {
                            if (!$item->organizationUnit) {
                                return '(not set)';
                            }
                            return $item->organizationUnit->ask_for_input ? $item->organizationUnit->name . " - <strong>" . $item->organization_unit_other . "</strong>" : $item->organizationUnit->name ?? '(not set)';
                        }
                    ],
                    [
                        //'attribute' => 'department.name',
                        'label' => 'Department',
                        'value' => function ($item) {
                            if (!$item->organizationUnit) {
                                return '(not set)';
                            }
                            return $item->organizationUnit->ask_for_input ? '' : $item->department->name ?? '(not set)';
                        }
                    ],
                    [
                        'attribute' => 'subDepartment.name',
                        'label' => 'Sub-Department',
                        'value' => function ($item) {
                            if (!$item->organizationUnit || !$item->department) {
                                return '(not set)';
                            }
                            return $item->organizationUnit->ask_for_input ? '' : $item->subDepartment->name ?? '(not set)';
                        }
                    ],
                    [
                        'attribute' => 'role',
                        'format' => 'raw',
                        'filter' => \app\models\enums\UserRoles::getDataList(),
                        'value' => function ($model) {
                            return \app\models\enums\UserRoles::getByValue($model->role)->label();
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => \app\models\enums\UserStatus::getDataList(),
                        'value' => function ($model) {
                            return \app\models\enums\UserStatus::getByValue($model->status)->text();
                        }
                    ],
                    [
                        'attribute' => 'approved',
                        'filter' => \skinka\php\TypeEnum\enums\YesNo::getDataList(),
                        'value' => function ($item) {
                            return $item->approved ? 'Yes' : 'No';
                        }
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update}'
                    ]
                ]
            ])
            ?>
        </div>
    </div>
</div>
