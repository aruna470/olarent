<?php

use yii\helpers\Html;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
        <div class="container">
            <?php
            foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                if ($key == 'error') {
                    $key = 'danger';
                }
                echo '<p></p>';
                echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
            }
            ?>
            <div class="login-page-content">
                <?php echo $content ?>
            </div>
        </div>
    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
