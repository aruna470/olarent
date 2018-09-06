<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$title = '' == $this->title ? Yii::$app->params['productName'] : Yii::$app->params['productName'] . ' - ' . $this->title;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <link rel="shortcut icon" href="<?= Yii::$app->view->theme->baseUrl ?>/img/favicon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>

        <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => Html::img(Yii::$app->view->theme->baseUrl . '/img/olarent-logo.png', ['class' => 'main-logo']),
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            if (!Yii::$app->getUser()->isGuest):
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'activateItems' => true,
                    'activateParents' => true,
                    'encodeLabels' => false,
                    'items' => [
                        ['label' => '<span class="glyphicon glyphicon-stats"></span> ' . Yii::t('app', 'Dashboard'), 'url' => ['/dashboard/dashboard'], 'visible' => Yii::$app->user->can('Dashboard.Dashboard')],
                        ['label' => '<span class="glyphicon glyphicon-user"></span> ' . Yii::t('app', 'Users'), 'url' => ['/user/reg-user-index'], 'visible' => Yii::$app->user->can('User.RegUserIndex')],
                        ['label' => '<span class="glyphicon glyphicon-home"></span> ' . Yii::t('app', 'Properties'), 'url' => ['/property/index'], 'visible' => Yii::$app->user->can('Property.Index')],
                        [
                            'label' => '<span class="glyphicon glyphicon-usd"></span> ' . Yii::t('app', 'Finance'),
                            'visible' => Yii::$app->user->canList(['Payment.Index', 'CompanyWallet.Index']),
                            'items' => [
                                ['label' => Yii::t('app', 'Tenant Payments'), 'url' => ['/payment/index'], 'visible' => Yii::$app->user->can('Payment.Index')],
                                ['label' => Yii::t('app', 'Owner Payouts'), 'url' => ['/payout/index'], 'visible' => Yii::$app->user->can('Payout.Index')],
                                ['label' => Yii::t('app', 'Company Wallet'), 'url' => ['/company-wallet/index'], 'visible' => Yii::$app->user->can('CompanyWallet.Index')],
                            ],
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-wrench"></span> ' . Yii::t('app', 'System'),
                            'visible' => Yii::$app->user->canList(['Permission.Index', 'Role.Index', 'User.Index']),
                            'items' => [
                                ['label' => Yii::t('app', 'Permissions'), 'url' => ['/permission/index'], 'visible' => Yii::$app->user->can('Permission.Index')],
                                ['label' => Yii::t('app', 'Roles'), 'url' => ['/role/index'], 'visible' => Yii::$app->user->can('Role.Index')],
                                ['label' => Yii::t('app', 'System Users'), 'url' => ['/user/index'], 'visible' => Yii::$app->user->can('User.Index')],
                            ],
                        ],
                    ],
                ]);
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'activateItems' => true,
                    'activateParents' => true,
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => '<span class="glyphicon glyphicon-user"></span> ' . Yii::$app->user->identity->firstName . ' ' . Yii::$app->user->identity->lastName,
                            'items' => [
                                ['label' => Yii::t('app', 'My Account'), 'url' => ['/user/my-account'], 'visible' => Yii::$app->user->can('User.MyAccount')],
                                ['label' => Yii::t('app', 'Change Password'), 'url' => ['/user/change-password'], 'visible' => Yii::$app->user->can('User.ChangePassword')],
                                ['label' => Yii::t('app', 'Logout'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
                            ],
                        ],
                    ],
                ]);
            endif;
            NavBar::end();
            ?>

            <div class="container page">
				<div class="card-breadcrumb">
					<?=
                        Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]);
                    ?>
				</div>
                <div class="card">
                    <div id="statusMsg"></div>
                    <?php
                    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                        if ($key == 'error') {
                            $key = 'danger';
                        }
                        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                    }
                    ?>

                    <?php if (null != $this->pageTitle): ?>
                        <div class="card-header">
                            <h2><?= $this->pageTitle ?>
                                <?php if (null != $this->pageTitleDescription): ?>
                                    <small><?= $this->pageTitleDescription ?></small>
                                <?php endif; ?>
                            </h2>
                        </div>
                    <?php endif; ?>
                    <?= $content ?>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p style="text-align:center"><?= Yii::$app->params['copyright'] ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
