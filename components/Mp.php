<?php

namespace app\components;

use MangoPay\KycPage;
use MangoPay\Libraries\Exception;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsBankWire;
use Yii;
use yii\base\Component;
use MangoPay\MangoPayApi;
use MangoPay\UserNatural;
use MangoPay\Wallet;
use MangoPay\KycDocument;
use MangoPay\PayIn;
use MangoPay\Money;
use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\Address;
use MangoPay\Transfer;
use MangoPay\PayOut;
use MangoPay\PayOutPaymentDetailsBankWire;


/**
 * Wrapper class for accessing MangoPay API
 */
class Mp extends Component
{
    public $clientId;
    public $clientPassword;
    public $tempPath;
    public $api;
    public $log;
    public $errorInfo;

    // KYC document types
    const IDENTITY_PROOF = 'IDENTITY_PROOF';

    // KYC document status types
    const VALIDATION_ASKED = 'VALIDATION_ASKED';

    // Payout statuses
    const PO_CREATED = 'CREATED';
    const PO_SUCCEEDED = 'SUCCEEDED';
    const PO_FAILED = 'FAILED';

    // Transfer statuses
    const TR_CREATED = 'CREATED';
    const TR_SUCCEEDED = 'SUCCEEDED';
    const TR_FAILED = 'FAILED';

    public function __construct($config)
    {
        $this->api = new MangoPayApi();
        $this->api->Config->ClientId = $config['clientId'];
        $this->api->Config->ClientPassword = $config['clientPassword'];
        $this->api->Config->TemporaryFolder = Yii::getAlias($config['tempPath']);
        $this->api->Config->DebugMode = false;
        $this->log = Yii::$app->appLog;

        parent::__construct();
    }

    /**
     * Create natural user
     * @param array $params Necessary parameters to create user
     * @return array
     */
    public function createNaturalUser($params)
    {
        $fieldMapping = [
            'Email' => 'email',
            'FirstName' => 'firstName',
            'LastName' => 'lastName',
            'Birthday' => 'birthDate',
            'Nationality' => 'nationality',
            'CountryOfResidence' => 'countryOfResidence',
            'IncomeRange' => 'incomeRange',
            'Occupation' => 'occupation'
        ];

        $userNatural = new UserNatural();

        foreach($fieldMapping as $key => $value) {
            if (isset($params[$value])) {
                if ($key == 'Birthday') {
                    $params[$value] = strtotime($params[$value]);
                }
                $userNatural->$key = $params[$value];
            }
        }

        $naturalUserResult = [];
        try {
            $naturalUserResult = $this->api->Users->Create($userNatural);
            $this->log->writeLog('MP user created.', [$naturalUserResult]);
        } catch (\Exception $e) {
            $this->log->writeLog('MP user create failed.', ['error' => $e->getMessage()]);
        }

        return $naturalUserResult;
    }

    /**
     * Update natural user
     * @param array $params Necessary parameters to create user
     * @return array
     */
    public function updateNaturalUser($params)
    {
        $fieldMapping = [
            'Id' => 'mpUserId',
            'Email' => 'email',
            'FirstName' => 'firstName',
            'LastName' => 'lastName',
            'Birthday' => 'birthDate',
            'Nationality' => 'nationality',
            'CountryOfResidence' => 'countryOfResidence',
            'IncomeRange' => 'incomeRange',
            'Occupation' => 'occupation'
        ];

        $userNatural = new UserNatural();

        foreach($fieldMapping as $key => $value) {
            if (isset($params[$value])) {
                if ($key == 'Birthday') {
                    $params[$value] = strtotime($params[$value]);
                }
                $userNatural->$key = $params[$value];
            }
        }

        $naturalUserResult = [];
        try {
            $naturalUserResult = $this->api->Users->Update($userNatural);
            $this->log->writeLog('MP user updated.', [$naturalUserResult]);
        } catch (\Exception $e) {
            $this->log->writeLog('MP user update failed.', ['error' => $e->getMessage()]);
        }

        return $naturalUserResult;
    }

    /**
     * Create MangoPay wallet
     * @param string $mpUserId MangoPay user id
     * @param string $currency Currency code
     * @param string $description Wallet description
     * @return array
     */
    public function createWallet($mpUserId, $currency, $description)
    {
        $wallet = new Wallet();
        $wallet->Owners = [$mpUserId];
        $wallet->Description = $description;
        $wallet->Currency = $currency;

        $walletResult = [];
        try {
            $walletResult = $this->api->Wallets->Create($wallet);
            $this->log->writeLog('Wallet created.', [$walletResult]);
        } catch (\Exception $e) {
            $this->log->writeLog('Wallet create failed.', ['error' => $e->getMessage()]);
        }

        return $walletResult;
    }

    /**
     * Retrieve MangoPay wallet details
     * @param string $walletId Wallet id
     * @return array
     */
    public function getWallet($walletId)
    {
        $res = [];

        try {
            $res = $this->api->Wallets->Get($walletId);
            $this->log->writeLog('Retrieve wallet details.', [$res]);
        } catch (\Exception $e) {
            $this->log->writeLog('Wallet details retrieval failed.', ['error' => $e->getMessage()]);
        }

        return $res;
    }

    /**
     * Create & Upload KYC document
     * @param string $mpUserId MangoPay user id
     * @param string $type KYC document type
     * @return array
     */
    public function createKycDocument($mpUserId, $type)
    {
        $kycDoc = new KycDocument();
        $kycDoc->Type = $type;

        $kycResult = [];
        try {
            $kycResult = $this->api->Users->CreateKycDocument($mpUserId, $kycDoc);
            $this->log->writeLog('KYC document created.', [$kycResult]);
        } catch (\Exception $e) {
            $this->log->writeLog('KYC document create failed.', ['error' => $e->getMessage()]);
        }

        return $kycResult;
    }

    /**
     * Update KYC document
     * @param string $mpUserId MangoPay user id
     * @param string $docId Document id
     * @return array
     */
    public function updateKycDocument($mpUserId, $docId)
    {
        $kycDoc = new KycDocument();
        $kycDoc->Id = $docId;
        $kycDoc->Status = self::VALIDATION_ASKED;

        $kycResult = [];
        try {
            $kycResult = $this->api->Users->UpdateKycDocument($mpUserId, $kycDoc);
            $this->log->writeLog('KYC document updated.', [$kycResult]);
        } catch (\Exception $e) {
            $this->log->writeLog('KYC document update failed.', ['error' => $e->getMessage()]);
        }

        return $kycResult;
    }

    /**
     * Upload document
     * @param string $mpUserId MangoPay user id
     * @param string $docId Document id
     * @parma string $fileData File content as base64 encoded string
     * @return array
     */
    public function uploadKycDocument($mpUserId, $docId, $fileData)
    {
        $status = false;
        $kycPage = new KycPage();
        $kycPage->File = $fileData;
        try {
            $res = $this->api->Users->CreateKycPage($mpUserId, $docId, $kycPage);
            $status = true;
            $this->log->writeLog('KYC document uploaded.', [$res]);
        } catch (\Exception $e) {
            $this->log->writeLog('KYC document upload failed.', ['error' => $e->getMessage()]);
        }

        return $status;
    }

    /**
     * Retrieve document list
     * @param string $mpUserId MangoPay user id
     * @return array
     */
    public function getKycDocuments($mpUserId)
    {
        $docList = [];

        try {
            $docList = $this->api->Users->GetKycDocuments($mpUserId);
        } catch (\Exception $e) {
            $this->log->writeLog('KYC document list retrieval failed.', ['error' => $e->getMessage()]);
        }

        return $docList;
    }

    /**
     * Create pay in bank wire.
     * @param string $mpUserId MangoPay user id
     * @param string $walletId MangoPay wallet id
     * @param string $currency Currency format
     * @param float $amount Expecting wire amount
     * @return array
     */
    public function createPayInBankWire($mpUserId, $mpWalletId, $currency, $amount)
    {
        $res = [];

        $payIn = new PayIn();
        $decDebFun = new Money();
        $decFees = new Money();
        $payInPaymentDetailsBankWire = new PayInPaymentDetailsBankWire();
        $payInExecutionDetailsDirect = new PayInExecutionDetailsDirect();

        $decDebFun->Currency = $currency;
        $decDebFun->Amount = $amount * 100;

        $decFees->Amount = 0;
        $decFees->Currency = $currency;

        $payInPaymentDetailsBankWire->DeclaredDebitedFunds = $decDebFun;
        $payInPaymentDetailsBankWire->DeclaredFees = $decFees;

        $payIn->AuthorId = $mpUserId;
        $payIn->CreditedUserId = $mpUserId;
        $payIn->CreditedWalletId = $mpWalletId;
        $payIn->PaymentType = 'BANK_WIRE';
        $payIn->PaymentDetails = $payInPaymentDetailsBankWire;
        $payIn->ExecutionType = 'DIRECT';
        $payIn->ExecutionDetails = $payInExecutionDetailsDirect;

        try {
            $res = $this->api->PayIns->Create($payIn);
            $this->log->writeLog('PayIn bank wire created.', [$res]);
        } catch (\Exception $e) {
            $this->log->writeLog('PayIn bank wire create failed.', ['error' => $e->getMessage()]);
        }

        return $res;
    }

    /**
     * Get pay in details.
     * @param string $id Pay in id
     * @return array
     */
    public function getPayIn($id)
    {
        $res = [];

        try {
            $res = $this->api->PayIns->Get($id);
        } catch (\Exception $e) {
            $this->log->writeLog('PayIn details retrieval failed.', ['error' => $e->getMessage()]);
        }

        return $res;
    }

    /**
     * Create bank account for user
     * @param string $ownerName Name of the bank account owner
     * @param string $mpUserId MangoPay user id
     * @param string $ownerAddress Address of the bank account owner
     * @param string $city Address City
     * @param string $country Country code
     * @param string $postalCode Postal code
     * @param string $iban IBAN code
     * @param string $type Bank account type.
     * @return array
     */
    public function createBankAccount($ownerName, $mpUserId, $ownerAddress, $iban, $city, $country, $postalCode, $type = 'IBAN')
    {
        $res = [];

        $bankAccount = new BankAccount();
        $bankAccountDetailsIban = new BankAccountDetailsIBAN();
        $address = new Address();

        $address->AddressLine1 = $ownerAddress;
        $address->City = $city;
        $address->Country = $country;
        $address->PostalCode = $postalCode;

        $bankAccountDetailsIban->IBAN = $iban;

        $bankAccount->OwnerName = $ownerName;
        $bankAccount->UserId = $mpUserId;
        $bankAccount->OwnerAddress = $address;
        $bankAccount->Type = $type;
        $bankAccount->Details = $bankAccountDetailsIban;

        try {
            $res = $this->api->Users->CreateBankAccount($mpUserId, $bankAccount);
            $this->log->writeLog('Bank account created.', [$res]);
        } catch (\Exception $e) {
            $this->errorInfo = $e->GetErrorDetails();
            $this->log->writeLog('Bank account create failed.', ['error' => $e->GetErrorDetails()]);
        }

        return $res;
    }

    /**
     * Disactivate a bank account
     * @param string $mpBankAccountId Bank account resource id
     * @return array
     */
    public function disActivateBankAccount($mpBankAccountId)
    {
        $res = [];

        $bankAccount = new BankAccount();
        $bankAccount->Id = $mpBankAccountId;
        //$bankAccount->

//        $res = $this->api->Users->B
    }

    /**
     * Transfer money form wallet to another wallet
     * @param string $authorId Id of the author
     * @param string $creditedUserId Account id of credited user
     * @param float $debitedAmount Transfer amount
     * @param string $currency Currency type
     * @param string $feesAmount Platform fees
     * @param string $debitedWalletId Debited wallet id
     * @param string $creditedWalletId Credited wallet id
     * @return array
     */
    public function transfer($authorId, $creditedUserId, $debitedAmount, $currency, $feesAmount, $debitedWalletId,
                             $creditedWalletId)
    {
        $res = [];

        $transfer = new Transfer();

        $debitedFunds = new Money();
        $debitedFunds->Amount = $debitedAmount * 100;
        $debitedFunds->Currency = $currency;

        $fees = new Money();
        $fees->Amount = $feesAmount * 100;
        $fees->Currency = $currency;

        $transfer->AuthorId = $authorId;
        $transfer->CreditedUserId = $creditedUserId;
        $transfer->DebitedFunds = $debitedFunds;
        $transfer->Fees = $fees;
        $transfer->DebitedWalletId = $debitedWalletId;
        $transfer->CreditedWalletId = $creditedWalletId;

        try {
            $res = $this->api->Transfers->Create($transfer);
            $this->log->writeLog('Transfer created.', [$res]);
        } catch (\Exception $e) {
            $this->log->writeLog('Transfer create failed.', ['error' => $e->getMessage()]);
        }

        return $res;
    }

    /**
     * Perform payout from wallet to user's bank account
     * @param string $authorId Id of the author
     * @param string $debitedWalletId Debited wallet id
     * @param float $debitedAmount Transfer amount
     * @param string $currency Currency type
     * @param string $feesAmount Platform fees
     * @param string $bankAccountId Account id of credited user
     * @param string $refId Unique reference for this payout
     * @return array
     */
    public function payOut($authorId, $debitedWalletId, $debitedAmount, $currency, $feesAmount, $bankAccountId, $refId)
    {
        $res = [];

        $payout = new PayOut();

        $debitedFunds = new Money();
        $debitedFunds->Amount = $debitedAmount * 100;
        $debitedFunds->Currency = $currency;

        $fees = new Money();
        $fees->Amount = $feesAmount * 100;
        $fees->Currency = $currency;

        $paymentDetailsBankWire = new PayOutPaymentDetailsBankWire();
        $paymentDetailsBankWire->BankAccountId = $bankAccountId;
        $paymentDetailsBankWire->BankWireRef = $refId;

        $payout->AuthorId = $authorId;
        $payout->DebitedWalletId = $debitedWalletId;
        $payout->DebitedFunds = $debitedFunds;
        $payout->Fees = $fees;
        $payout->PaymentType = 'BANK_WIRE';
        $payout->MeanOfPaymentDetails = $paymentDetailsBankWire;

        try {
            $res = $this->api->PayOuts->Create($payout);
            $this->log->writeLog('Payout created.', [$res]);
        } catch (\Exception $e) {
            $this->log->writeLog('Payout create failed.', ['error' => $e->getMessage()]);
        }

        return $res;
    }

    /**
     * Possible income ranges defined by MangoPay
     * @return array
     */
    public function getIncomeRanges()
    {
        return [
            1 => Yii::t('app', 'Less than 18K €'),
            2 => Yii::t('app', 'Between 18 and 30K €'),
            3 => Yii::t('app', 'Between 30 and 50K €'),
            4 => Yii::t('app', 'Between 50 and 80K €'),
            5 => Yii::t('app', 'Between 80 and 120K €'),
            6 => Yii::t('app', 'Greater than 120K €'),
        ];
    }

    /**
     * Retrieve income range identifiers
     * @return array
     */
    public static function getIncomeRangeIdentifiers()
    {
        return array_keys(self::getIncomeRanges());
    }

    /**
     * Possible nationalities defined by MangoPay
     * @return array
     */
    public function getNationalities()
    {
        return [
            'FR' => Yii::t('app', 'France')
        ];
    }

    /**
     * Retrieve nationality identifiers
     * @return array
     */
    public static function getNationalityIdentifiers()
    {
        return array_keys(self::getNationalities());
    }

    /**
     * Possible country codes defined by MangoPay
     * @return array
     */
    public function getCountryCodes()
    {
        return [
            'FR' => Yii::t('app', 'France')
        ];
    }

    /**
     * Retrieve country code identifiers
     * @return array
     */
    public static function getCountryCodeIdentifiers()
    {
        return array_keys(self::getCountryCodes());
    }

    /**
     * Retrieve error field
     * @return string
     */
    public function getErrorField()
    {
        $errorInfo = (array) $this->errorInfo;
        $errorFields = (array) $errorInfo['Errors'];

        reset($errorFields);
        $errorField = key($errorFields);

        return $errorField;
    }
}