<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title></title>
</head>
<body style="margin: 0; padding: 0; background-color: #ccc; padding: 30px; font-family: 'Helvetica', Arial, sans-serif;">

<table align="center" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
    <tr>
        <td>
            <!--Header-->
            <table width="100%" style="width:92%!important; background-color: #4c176d; padding: 4%;">
                <tr>
                    <td>
                        <img src="<?=Yii::$app->params['logoUrlInEmail']?>" title="Olarent" alt="Olarent" style="width: 150px;">
                    </td>
                </tr>
            </table>

            <!--Content-->
            <table width="100%" style="width:92%!important; border-bottom: 1px solid #cbdadf; background-color: #fff; padding: 4%;">
                <tr>
                    <td style="font-size: 14px; color: #464646; text-align: left;">
                        <?php echo $content ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14px; color: #464646; text-align: left;">
                        <p><span style="color:#4c176d;font-weight: bold"><?= Yii::t('mail', 'The Olarent Team')?></span><br>
                        <a href="http://olarent.io">www.olarent.io</a></p>
                    </td>
                </tr>
            </table>

            <!--Footer-->
            <table width="100%" style="width:92%!important; background-color: #fff;">
                <tr>
                    <td style="font-family: arial, helvetica, verdana, sans-serif; text-align: center; font-size: 10px; padding: 10px; color: #4c176d;">
                        <p><span style="font-weight: bold;"><?= Yii::t('mail', 'Olarent property management system.')?></span>
                            <br><?= Yii::t('mail', 'Tel:') . Yii::$app->params['telephone'] ?> - <?= Yii::t('mail', 'Email:')?> <a href="mailto:<?=  Yii::$app->params['supportEmail']?>" style="color: #464646;"> <?=  Yii::$app->params['supportEmail']?></a>
                        </p>
                        <p>
                            <a href="<?= Yii::$app->params['emailUnsubLink'] ?>" style="color: #464646;"><?= Yii::t('mail', 'Unsubscribe')?></a>
                        </p>
                        <p><?= Yii::t('mail', 'Copyright © {year} Olarent. All Rights Reserved.', ['year' => date('Y')])?></p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>