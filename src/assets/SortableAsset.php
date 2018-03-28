<?php

namespace app\assets;

use yii\web\AssetBundle;

class SortableAsset extends AssetBundle
{
    public $sourcePath = '@bower/sortablejs';
    public $css = [];
    public $js = [
        'Sortable.min.js',
        'jquery.binding.js'
    ];
    public $depends = [];

    public $publishOptions = [];
}
