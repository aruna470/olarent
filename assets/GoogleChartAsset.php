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
class GoogleChartAsset extends AssetBundle
{
    public function init()
    {
        parent::init();
    }

    public function registerAssetFiles($view)
    {
        $view->registerJsFile('https://www.gstatic.com/charts/loader.js');
        parent::registerAssetFiles($view);
    }
}


