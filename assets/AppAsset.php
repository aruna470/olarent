<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/default/css/bootstrap.min.css',
        'extensions/fancybox/source/jquery.fancybox.css',
        'themes/default/css/style.css',
        'extensions/owlcarousel/assets/owl.carousel.css',
        'extensions/owlcarousel/assets/owl.theme.default.min.css',
    ];
    public $js = [
        'themes/default/js/bootstrap.min.js',
        'themes/default/js/bootbox.min.js',
        'js/Util.js',
        'extensions/fancybox/source/jquery.fancybox.pack.js',
        'extensions/toastMessage/jquery.toaster.js',
        'extensions/owlcarousel/owl.carousel.min.js',
        'extensions/bootstrap-file-input/bootstrap.file-input.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
