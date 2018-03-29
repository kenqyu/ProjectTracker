<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\modules\jobs\models\JobSearchForm
 */
use app\models\enums\UserRoles;
use app\models\Job;
use app\widgets\DatePicker;
use kartik\grid\GridView;

$this->title = 'Jobs';
\app\modules\jobs\assets\JobsAsset::register($this);
?>
<div class="container-fluid">
    <div class="jobs index">
        <div class="row">
            <div class="col-md-12">
                <?= \app\widgets\Alert::widget() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div data-show="4"
                     class="row lists <?= Yii::$app->session->get('list_view', false) ? 'list-view' : 'grid-view' ?>">
                    <?= $this->render('partial/index/_list_view', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>