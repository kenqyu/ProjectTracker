<?php
/**
 * @var $this \yii\web\View
 * @var $row array
 */
$rowId = \Ramsey\Uuid\Uuid::uuid4()->toString();
?>
<div class="builder_row" data-id="<?= $rowId ?>">
    <div class="dragger_container">
        <div class="dragger">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </div>
    </div>
    <div class="row-configuration_container">
        <div class="row_name row">
            <div class="col-md-12">
                <?= \yii\bootstrap\Html::textInput(
                    'BuilderForm[content][rows][' . $rowId . '][name]',
                    $row['name'],
                    ['class' => 'form-control', 'placeholder' => 'Row Label']
                ) ?>
            </div>
        </div>
        <div class="columns">
            <?php
            if (isset($row['columns'])) {
                foreach ($row['columns'] as $column) {
                    echo $this->render('_column', ['column' => $column, 'rowId' => $rowId]);
                }
            }
            ?>
        </div>

        <div class="row actions">
            <div class="col-md-12 text-center">
                <a href="#" class="btn btn-danger delete_row">Delete row</a>
            </div>
        </div>
    </div>
</div>
