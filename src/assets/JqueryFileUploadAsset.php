<?php
namespace app\assets;

use yii\web\AssetBundle;

class JqueryFileUploadAsset extends AssetBundle
{
    public $sourcePath = '@bower/blueimp-file-upload/';
    public $css = [
        'css/jquery.fileupload.css'
    ];
    public $js = [
        'js/jquery.iframe-transport.js',
        'js/vendor/jquery.ui.widget.js',
        'js/jquery.fileupload.js'
    ];
    public $depends = [];
}
