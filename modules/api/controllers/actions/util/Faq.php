<?php

namespace app\modules\api\controllers\actions\util;


use Yii;
use yii\base\Action;
use app\components\Aws;
use app\modules\api\components\Messages;
use app\models\UploadFile;
use app\modules\api\components\ApiStatusMessages;

class Faq extends Action
{
    public function run()
    {
        $params = Yii::$app->request->get();
        $lang = @$params['lang'];

        $faqList = [
            1 => [
                'question' => Yii::t('faq', 'Why should I pay my rent with a credit card ?', [], $lang),
                'answers' => [
                    1 => Yii::t('faq', 'Because it\'s more convenient than writing a check, finding a letter and a stamp
and not to forget to mail it, all of that in time every month.', [], $lang),
                    2 => Yii::t('faq', 'It\'s the only way to pay your rent online, mobile payment is seamless with a credit card or wire payment.', [], $lang),
                    3 => Yii::t('faq', 'Your credit card has advantages a check does not have, your rent is taken off
your account immediately with a check, with your credit card you can benefit from your monthly debit for example or any airline miles that your card is associated with', [], $lang)
                ]
            ],
            2 => [
                'question' => Yii::t('faq', 'How much does this cost ?', [], $lang),
                'answers' => [
                    1 => Yii::t('faq', 'Wire payment is 0,5% per transaction, while credit cards are 2% per transaction.', [], $lang),
                ]
            ],
            3 => [
                'question' => Yii::t('faq', 'My landlord doesn’t use Olarent, or does not even have a smartphone, can I still Pay with Olarent?', [], $lang),
                'answers' => [
                    1 => Yii::t('faq', 'Absolutely. Olarent does not require your landlord to be signed up to use Olarent.
We send your landlord a wire payment on your behalf every month so you don’t have to.', [], $lang),
                    2 => Yii::t('faq', 'As long as you provide us the bank information (IBAN/RIB) of your landlord we
will wire him the money, even if he does not use Olarent or Internet at all.', [], $lang),
                ]
            ],
            4 => [
                'question' => Yii::t('faq', 'Why should I create a landlord account ?', [], $lang),
                'answers' => [
                    1 => Yii::t('faq', 'A landlord after posting online his place for rent receives between 40 to 70
emails, with heavy attached document and no referals. With Olarent referals and notations system he is able to quickly
identify a renter and build a trusted relationship. Every month after the rental payment the landlord can evaluate his
tenant with a note, that will strenghten the system and the relationship.', [], $lang),
                ]
            ],
            5 => [
                'question' => Yii::t('faq', 'Why should I create a Tenant account ?', [], $lang),
                'answers' => [
                    1 => Yii::t('faq', 'Finding a place to rent can be very hard.
Olarent enables you to show your renter profile to potential landlords, collect trusted referals from former landlords,
show your social media profile so potential landlords will be more likely to trust you and give you access to their properties.', [], $lang),
                ]
            ],
        ];

        $finalList = [];

        foreach ($faqList as $item) {
            $ans = [];
            foreach ($item['answers'] as $answer) {
                $ans[] = str_replace("\r\n", "", $answer);
            }
            $finalList[] = [
                'question' => str_replace("\r\n", "", $item['question']),
                'answers' => $ans
            ];
        }

        $this->controller->sendResponse($finalList);
    }
}
?>