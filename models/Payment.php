<?php

namespace app\models;

use Yii;
use yii\db\Query;
use app\models\Base;

/**
 * This is the model class for table "Payment".
 *
 * @property integer $id
 * @property integer $payerUserId
 * @property integer $payeeUserId
 * @property integer $propertyId
 * @property double $amount
 * @property integer $type
 * @property string $adyenPspReference
 * @property string $adyenTransactionReference
 * @property string $createdAt
 *
 * @property User $payerUser
 */
class Payment extends Base
{
    const TYPE_KEY_MONEY = 1;
    const TYPE_RENTAL = 2;

    const PAYOUT_NOT_PROCESSED = 0;
    const PAYOUT_PROCESSED = 1;

    public $total;
    public $propertyName;
    public $propertyCode;

    public $paymentTypes;
    public $paymentId;

    public function init()
    {
        $this->paymentTypes = [
            self::TYPE_KEY_MONEY => Yii::t('app', 'Key Money Charge'),
            self::TYPE_RENTAL => Yii::t('app', 'Rental Charge'),
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payerUserId', 'payeeUserId', 'propertyId', 'type'], 'required'],
            [['payerUserId', 'payeeUserId', 'propertyId', 'type'], 'integer'],
            [['amount'], 'number'],
            [['createdAt'], 'safe'],
            [['adyenPspReference'], 'string', 'max' => 30],
            [['adyenTransactionReference'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payerUserId' => Yii::t('app', 'Payer User ID'),
            'payeeUserId' => Yii::t('app', 'Payee User ID'),
            'propertyId' => Yii::t('app', 'Property ID'),
            'amount' => Yii::t('app', 'Amount (EUR)'),
            'type' => Yii::t('app', 'Payment Type'),
            'adyenPspReference' => Yii::t('app', 'Adyen Psp Reference'),
            'adyenTransactionReference' => Yii::t('app', 'Adyen Transaction Reference'),
            'createdAt' => Yii::t('app', 'Created At'),
            'propertyName' => Yii::t('app', 'Property Name'),
            'propertyCode' => Yii::t('app', 'Property Code'),
            'commssion' => Yii::t('app', 'Commission (EUR)'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'payerUserId'])
            ->from(User::tableName() . ' payerU');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayeeUser()
    {
        return $this->hasOne(User::className(), ['id' => 'payeeUserId'])
            ->from(User::tableName() . ' payeeU');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'propertyId']);
    }

    /**
     * Calculate total payments made for each day
     * @param integer $days Number of back days from now
     * @return array
     */
    public function getTotPaymentsByDate($days = 7)
    {
        $data = [];
        $data[] = [Yii::t('app', 'Date'), Yii::t('app', 'Total')];
        for ($i=0; $i<$days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $query = new Query();
            $total = $query->from(self::tableName())
                ->andWhere('DATE(createdAt) = :createdAt', [':createdAt' => $date])
                ->andWhere('type = :type', [':type' => Payment::TYPE_RENTAL])
                ->sum('amount');
            $data[] = [$date, (int)$total];
        }

        return $data;
    }

    /**
     * Add payment details
     * @param integer $payerUserId User who makes the payment
     * @param integer $payeeUserId User who receives the payment
     * @param integer $propertyId Property id
     * @param float $amount Transaction amount in USD
     * @param integer $type Transaction type ex:keymoney, rent
     * @param float $type Transaction type ex:keymoney, rent
     * @param float $commission Commission
     * @param array $transactionData Additional transaction data
     * @return array
     */
    public function addPayment($payerUserId, $payeeUserId, $propertyId, $amount, $type, $commission, $transactionData)
    {
        $model = new Payment();
        $model->payerUserId = $payerUserId;
        $model->payeeUserId = $payeeUserId;
        $model->propertyId = $propertyId;
        $model->amount = $amount;
        $model->type = $type;
        $model->adyenPspReference = @$transactionData['pspReference'];
        $model->adyenTransactionReference = @$transactionData['reference'];
        $model->paymentForDate = @$transactionData['paymentForDate'];
        $model->currencyType = @$transactionData['currency'];
        $model->commssion = $commission;
        $model->percentage = Yii::$app->params['commission'];
        $model->stripeReference = @$transactionData['stripeReference'];

        return $model->saveModel();
//        if ($model->saveModel()) {
//            $this->paymentId = $model->id;
//            return true;
//        }
//
//        return false;
    }

    /**
     * Calculate pending and received payment of owner for current month
     * @param integer $userId User id of the owner
     * @return array
     */
    public function getOwnerCurMonthPaymentSummary($userId)
    {
        $pending = 0;

        $properties = Property::find()
            ->andWhere(['ownerUserId' => $userId])
            ->andWhere(['status' => Property::STATUS_NOT_AVAILABLE])
            ->all();

        if (!empty($properties)) {
            foreach ($properties as $property) {
                $payment = Payment::find()
                    ->andWhere(['payeeUserId' => $userId])
                    ->andWhere('MONTH(paymentForDate) = :month', [':month' => date('m')])
                    ->andWhere(['propertyId' => $property->id])
                    ->andWhere(['type' => Payment::TYPE_RENTAL])
                    ->one();

               if (empty($payment)) {
                   $pending += $property->cost;
               }
            }
        }

        $received = Payment::find()
            ->andWhere(['payeeUserId' => $userId])
            ->andWhere('MONTH(createdAt) = :month', [':month' => date('m')])
            ->andWhere('YEAR(createdAt) = :year', [':year' => date('Y')])
            ->andWhere(['type' => Payment::TYPE_RENTAL])
            ->sum('amount');

        // TODO:Remove following lines
//        $received = 100;
//        $pending = 50;

        return ['received' => $received, 'pending' => $pending];
    }

    /**
     * Calculate income of each month
     * @param integer $userId User id of the owner
     * @return array
     */
    public function getMonthlyIncomeSummary($userId)
    {
        $incomeSummary = [];
        $month = date('m');
        $year = date('Y');

        $user = User::findOne($userId);
        $userJoinedDateTs = strtotime($user->createdAt);
        $userJoinedMonthTs = strtotime(date('Y-m', $userJoinedDateTs));

        for ($i=0; $i<12; $i++) {
            $thisMonthTs = strtotime("{$year}-{$month}");
            if ($thisMonthTs >= $userJoinedMonthTs) {
                $income = Payment::find()
                    ->andWhere('MONTH(createdAt) = :month', [':month' => $month])
                    ->andWhere('YEAR(createdAt) = :year', [':year' => $year])
                    ->andWhere(['payeeUserId' => $userId])
                    ->andWhere(['type' => Payment::TYPE_RENTAL])
                    ->sum('amount');

                $monthName = $this->getMonthName($month);
                $income = number_format($income, 2, '.', '');
                $incomeSummary[] = ["month" => $monthName, "monthNum" => $month,  "income" => ("" == $income ? 0 : $income)];

                $month--;

                if ($month == 0) {
                    $month = 12;
                    $year--;
                }
            } else {
                break;
            }
        }

        return $this->fillMonths(array_reverse($incomeSummary));
    }

    /**
     * Fill future months if there are no past data
     * @param mixed $incomeSummary Current income summary details
     * @return array
     */
    public function fillMonths($incomeSummary)
    {
        if (count($incomeSummary) < 12) {
            $lastMonthInfo = end($incomeSummary);
            $month = (int)$lastMonthInfo['monthNum'] + 1;
            $remMonths = 12 - count($incomeSummary);

            for ($i=0; $i<$remMonths; $i++) {
                $monthName = $this->getMonthName($month);
                $incomeSummary[] = ["month" => $monthName, "monthNum" => $month,  "income" => 0];
                $month++;
                if ($month == 13) {
                    $month = 1;
                }
            }
        }

        return $incomeSummary;
    }

    /**
     * Get short name of the month by month number
     * @param integer $monthNumber Month number
     * @return string Short name of the month
     */
    public function getMonthName($monthNumber)
    {
        $dt = \DateTime::createFromFormat('!m', $monthNumber);
        return strtoupper(substr($dt->format('F'),0,3));
    }

    /**
     * Get pending payout to be processed.
     * @param integer $pageNo Pagination number
     * @param integer $limit Number of records
     * @return mixed $results
     */
    public function getPendingPayouts($pageNo, $limit = 50)
    {
        $results = Payment::find()
            ->andWhere(['isPayoutProcessed' => self::PAYOUT_NOT_PROCESSED])
            ->joinWith('property')
            ->limit($limit)
            ->offset($pageNo * $limit)
            ->all();

        return $results;
    }
}
