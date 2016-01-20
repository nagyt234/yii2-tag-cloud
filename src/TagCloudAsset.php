<?php
namespace asu\tagcloud;

use yii\web\AssetBundle;

class TagCloudAsset extends AssetBundle
{

    public $sourcePath = '@asu/tagcloud/assets';

    public $css = [
        'css/tag-cloud.css'
    ];
}