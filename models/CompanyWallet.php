<?php

namespace app\models;

use Yii;
use app\models\Base;
use app\components\Aws;
use app\components\Mp;

/**
 * This is the model class for table "CompanyWallet".
 *
 * @property integer $id
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $birthdate
 * @property string $nationality
 * @property string $countryOfResidence
 * @property integer $incomeRange
 * @property string $occupation
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $createdById
 * @property string $mpUserId
 * @property string $mpWalletId
 * @property string $address
 */
class CompanyWallet extends Base
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const KYC_DOCUMENT_CREATE = 'kycCreate';

    const COMP_ID_FILE_NAME = 'comp_id_{timestamp}.{ext}';

    // Proof document types
    const DT_IDENTITY = 1;

    public $idFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CompanyWallet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Common
            [['birthDate', 'createdAt', 'updatedAt'], 'safe'],
            [['incomeRange', 'createdById'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['email'], 'string', 'max' => 60, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['firstName'], 'string', 'max' => 20, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['lastName', 'occupation'], 'string', 'max' => 30, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['nationality', 'countryOfResidence'], 'string', 'max' => 3, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['mpUserId', 'mpWalletId'], 'string', 'max' => 10, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['idFile'], 'file', 'extensions' => 'jpg, png, pdf, jpeg', 'mimeTypes' => 'image/jpeg, image/png, application/pdf',
                'on' => [self::KYC_DOCUMENT_CREATE]],

            // Create
            [['email', 'firstName', 'lastName', 'birthDate', 'nationality', 'countryOfResidence', 'createdAt', 'createdById',
                'address'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],

            // KYC Create
            [['idFile'], 'required', 'on' => [self::KYC_DOCUMENT_CREATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'firstName' => Yii::t('app', 'First Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'birthDate' => Yii::t('app', 'Birthdate'),
            'nationality' => Yii::t('app', 'Nationality'),
            'countryOfResidence' => Yii::t('app', 'Country Of Residence'),
            'incomeRange' => Yii::t('app', 'Income Range'),
            'occupation' => Yii::t('app', 'Occupation'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'createdById' => Yii::t('app', 'Created By ID'),
            'mpUserId' => Yii::t('app', 'Mp User ID'),
            'mpWalletId' => Yii::t('app', 'Mp Wallet ID'),
            'address' => Yii::t('app', 'Address'),
            'idFile' => Yii::t('app', 'Identity File'),
            'docType' => Yii::t('app', 'Document Type'),
        ];
    }

    /**
     * Format company id file name for S3 upload
     * @param string $fileName Uploaded file name
     * @return string
     */
    public function getCompIdFileName($fileName)
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        return str_replace(['{timestamp}', '{ext}'], [time(), $ext], self::COMP_ID_FILE_NAME);
    }

    /**
     * Add Kyc document to JSON object
     * @param array $curList Current document list
     * @param string $fileName S3 uploaded file name
     * @param string $docId Document id returned by MangoPay
     * @param string $createdAt Created date & time
     * @param integer $docType Document type
     * @return string
     */
    public function addKycDocument($curList, $fileName, $docId, $createdAt, $docType = self::DT_IDENTITY)
    {
        $curList[] = [
            'fileName' => $fileName,
            'docId' => $docId,
            'createdAt' => $createdAt,
            'docType' => $docType
        ];

        return $curList;
    }

    /**
     * Retrieve KYC document list
     * @param array $curList Current document list
     * @param string $mpUserId MangoPay userId
     * @param Mp $mp MangoPay object
     * @return string
     */
    public function getKycDocuments($curList, $mpUserId, $mp)
    {
        $aws = new Aws();
        $newList = [];

        $docs = $mp->getKycDocuments($mpUserId);

        if (!empty($curList)) {
            foreach ($curList as $curItem) {
                $matchingEntry = $this->findMpEntry($docs, $curItem['docId']);
                $newList[] = array_merge($curItem, [
                    'fileUrl' => $aws->s3GetObjectUrl($curItem['fileName']),
                    'status' => $matchingEntry->Status
                ]);
            }
        }

        return $newList;
    }

    /**
     * Find matching MangoPay entry with the record in our side
     * @param array $curList Current document list
     * @param string $mpUserId MangoPay userId
     * @param Mp $mp MangoPay object
     * @return string
     */
    public function findMpEntry($mpDocResults, $mpDocId)
    {
        $matchingEntry = [];
        foreach ($mpDocResults as $res) {
            if ($res->Id == $mpDocId) {
                $matchingEntry = $res;
                break;
            }
        }

        return $matchingEntry;
    }

    /**
     * Retrieve document types
     * @return array
     */
    public function getDocTypes()
    {
        return [
            self::DT_IDENTITY => Yii::t('app', 'Identity file')
        ];
    }

    /**
     * Retrieve existing document id by type
     * @param array $docList Existing document list
     * @param integer $type Document type
     * @return string document id
     */
    public function getDocId($docList, $type)
    {
        $docId = '';
        if (!empty($docList)) {
            foreach ($docList as $doc) {
                if ($doc->docType == self::DT_IDENTITY) {
                    $docId = $doc->docId;
                    break;
                }
            }
        }

        return $docId;
    }
}
