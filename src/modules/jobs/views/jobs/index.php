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
            <div class="col-md-8">
                <?php
                \yii\bootstrap\Modal::begin([
                    'id' => 'add',
                    'header' => 'New Job'
                ]);
                ?>
                <?= $this->render('partial/_create_form') ?>
                <?php \yii\bootstrap\Modal::end() ?>
                <a href="#add" class="add_project btn btn-lg btn-primary">Add New Project</a>

                <?php if (Yii::$app->user->identity->role >= UserRoles::MANAGER) { ?>
                    <?php
                    \yii\bootstrap\Modal::begin([
                        'id' => 'saved_search',
                        'header' => 'Saved Searches'
                    ]);
                    ?>
                    <?= $this->render('partial/_saved_searches') ?>
                    <?php \yii\bootstrap\Modal::end() ?>

                    <div id="search-form-outer">
                        <?php $sForm = \yii\bootstrap\ActiveForm::begin([
                            'id' => 'search-form',
                            'action' => ['text-search'],
                            'method' => 'get'
                        ]) ?>
                        <?= $sForm->field(new \app\modules\jobs\models\SearchForm(),
                            'term')->textInput(['placeholder' => 'Search Job Name and Description...'])->label(false) ?>
                        <button class="btn btn-lg btn-primary"><i class="fa fa-search"></i></button>
                        <?php \yii\bootstrap\ActiveForm::end() ?>
                        <br>
                        <a id="search_link" href="#search" data-toggle="modal">Advanced Search</a> /

                        <a id="saved_search_link" href="#saved_search" data-toggle="modal">Saved Searches</a>
                    </div>


                <?php } ?>
            </div>

            <?php
            if (Yii::$app->request->get('list_view') != null &&
                Yii::$app->request->get('list_view') != Yii::$app->session->get('list_view')
            ) {
                Yii::$app->session->set('list_view', Yii::$app->request->get('list_view'));
            }
            ?>


            <div class="col-md-4 text-right view_choose">
                <a href="/?list_view=0" class="<?= Yii::$app->session->get('list_view', false) ? '' : 'active' ?>"><i
                            class="fa fa-th"></i> Grid View</a>
                <a href="/?list_view=1" class="<?= Yii::$app->session->get('list_view', false) ? 'active' : '' ?>"><i
                            class="fa fa-list"></i> List View</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php if (Yii::$app->user->identity->role >= UserRoles::MANAGER) { ?>
                    <form class="view_filter" action="/">
                        <label>
                            <input type="radio" name="all_projects"
                                   value="0" <?= Yii::$app->session->get('all_projects', 0) == 0 ? 'checked' : '' ?>> My
                            projects
                        </label>
                        <label>
                            <input type="radio" name="all_projects"
                                   value="1" <?= Yii::$app->session->get('all_projects', 0) == 1 ? 'checked' : '' ?>>
                            All
                            projects
                        </label>
                    </form>
                <?php } ?>
            </div>
            <div class="col-md-6 text-right">
                <?php if (!Yii::$app->session->get('list_view', false)) { ?>
                    <a href="#" class="btn additionalColumns">Show more columns</a>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= \app\widgets\Alert::widget() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div data-show="4"
                     class="row lists <?= Yii::$app->session->get('list_view', false) ? 'list-view' : 'grid-view' ?>">
                    <?php if (Yii::$app->session->get('list_view', false)) {
                        echo $this->render('partial/index/_list_view', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider
                        ]);
                    } else {
                        echo $this->render('partial/index/_column_view', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'model' => isset($model) ? $model : null
                        ]);
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>