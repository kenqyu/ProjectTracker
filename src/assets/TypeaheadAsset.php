<?php
namespace app\assets;

use yii\web\AssetBundle;

class TypeaheadAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap3-typeahead/';
    public $css = [
    ];
    public $js = [
        'bootstrap3-typeahead.min.js'
    ];
    public $depends = [];
}
