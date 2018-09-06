<?php
namespace app\modules\api\components;

use yii\base\Component;

/**
 * Messages class, Which prepares reply json msgs.
 */

class Messages extends Component
{
    /**
     * Common response message
     * @param string $code Status code
     * @param string $message Status message
     * @param array $extraParams Extra params to be sent along with common response
     * @return mixed
     */
    public static function commonStatus($code, $message = '', $extraParams = [])
    {
        $msg = [
            'code' => $code,
            'message' => $message
        ];

        if (!empty($extraParams)) {
            $msg['extraParams'] = $extraParams;
        }

        return $msg;
    }

    /**
     * User response message
     * @param user $user User object
     * @param array $files Array of files associated with user
     * @return mixed
     */
    public static function user($user, $files)
    {
        return [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'profileImage' => $user->getProfileImg(true),
            'profileImageOrig' => $user->profileImage,
            'profileImageThumb' => $user->getProfileImgThumbnail(true),
            'profileImageThumbOrig' => $user->profileImageThumb,
            'email' => $user->email,
            'type' => $user->type,
            'fbId' => $user->fbId,
            'fbAccessToken' => $user->fbAccessToken,
            'linkedInId' => $user->linkedInId,
            'linkedInAccessToken' => $user->linkedInAccessToken,
            'gplusId' => $user->gplusId,
            'gplusAccessToken' => $user->gplusAccessToken,
            'phone' => $user->phone,
            'bankAccountNo' => $user->bankAccountNo,
            'bankName' => $user->bankName,
            'bankAccountName' => $user->bankAccountName,
            'iban' => $user->iban,
            'swift' => $user->swift,
            'files' => $files,
            'rating' => $user->rating,
            'dob' => $user->dob,
            'profDes' => $user->profDes,
            'companyRegNum' => $user->companyRegNum,
            'companyType' => $user->companyType,
            'language' => null == $user->language ? $user::EN_US : $user->language,
            'companyName' => $user->companyName
        ];
    }

    /**
     * User response message with minimum data set
     * @param user $user User object
     * @param array $include Include other attributes if necessary
     * @return mixed
     */
    public static function userMin($user, $include = [])
    {
        $msg = [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'profileImage' => $user->getProfileImg(true),
            'profileImageThumb' => $user->getProfileImgThumbnail(true),
            'email' => $user->email,
            'phone' => $user->phone,
            'type' => $user->type,
            'rating' => $user->rating,
            'profDes' => $user->profDes,
            'companyType' => $user->companyType,
            'language' => null == $user->language ? $user::EN_US : $user->language,
            'companyName' => $user->companyName
        ];

        foreach ($include as $attrib) {
            $msg[$attrib] = $user->$attrib;
        }

        if (null != $user->isRequestedForReview) {
            // Need to be sent only with my owners list
            $msg['isRequestedForReview'] = $user->isRequestedForReview;
        }

        return $msg;
    }

    /**
     * Property response message
     * @param property $property Property object
     * @param array $owner Ownere details
     * @param array $tenant Tenant details
     * @param array $extraParams Additional parameters
     * @return array
     */
    public static function property($property, $owner, $tenant, $extraParams = [])
    {
        $msg = [
            'id' => $property->id,
            'owner' => $owner,
            'tenant' => $tenant,
            'code' => $property->code,
            'name' => $property->name,
            'description' => $property->description,
            'address' => $property->address,
            'city' => $property->city,
            'cost' => $property->cost,
            'status' => $property->status,
            'imageName' => $property->imageName,
            'imageUrl' => $property->getImageUrl(),
            'thumbImageUrl' => $property->getThumbImageUrl(),
            'zipCode' => $property->zipCode,
            'noOfRooms' => $property->noOfRooms,
            'size' => $property->size,
            'keyMoney' => $property->keyMoney,
            'currentRentDueAt' => $property->currentRentDueAt,
            'commisionPlan' => $property->commissionPlan,
            'isOnBhf' => $property->isOnBhf,
            'payDay' => $property->payDay,
            'duration' => $property->duration
        ];

        if (isset($extraParams['imageList'])) {
            $msg['images'] = $property->getImageList();
        }

        if (isset($extraParams['lastPaymentStatus'])) {
            $msg['lastPaymentStatus'] = $extraParams['lastPaymentStatus'];
        }

        if (isset($extraParams['paymentDate'])) {
            $msg['paymentDate'] = $extraParams['paymentDate'];
        }

        if (isset($extraParams['isEditable'])) {
            $msg['isEditable'] = $extraParams['isEditable'];
        }

        if (isset($extraParams['totalPendingPayments'])) {
            $msg['totalPendingPayments'] = $extraParams['totalPendingPayments'];
        }

        if (isset($extraParams['payDay'])) {
            $msg['payDay'] = $extraParams['payDay'];
        }

        if (isset($extraParams['paymentDueAt'])) {
            $msg['paymentDueAt'] = $extraParams['paymentDueAt'];
        }

        if (isset($extraParams['payNowEnable'])) {
            $msg['payNowEnable'] = $extraParams['payNowEnable'];
        }

        return $msg;
    }

    /**
     * Property request
     * @param PropertyRequest $propertyRequest Property request object
     * @param array $owner Ownere details
     * @param array $tenant Tenant details
     * @param array $property Property details
     * @return array
     */
    public static function propertyRequest($propertyRequest, $owner, $tenant, $property)
    {
        return [
            'id' => $propertyRequest->id,
            'code' => $propertyRequest->code,
            'tenantUserId' => $propertyRequest->tenantUserId,
            'payDay' => $propertyRequest->payDay,
            'bookingDuration' => $propertyRequest->bookingDuration,
            'status' => $propertyRequest->status,
            'owner' => $owner,
            'tenant' => $tenant,
            'property' => $property
        ];
    }

    /**
     * Search result
     * @param integer $total Total record count
     * @param array $data Result set
     * @return array
     */
    public static function searchResult($total, $data)
    {
       return [
            'total' => $total,
            'data' => $data
        ];
    }

    /**
     * Authentication response message
     * @param string $commonResponseMsg Common response object data
     * @param string $token User access token
     * @param array $user User details
     * @return mixed
     */
    public static function authenticationResponse($commonResponseMsg, $token, $user)
    {
        return [
            'status' => $commonResponseMsg,
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * File response message
     * @param File $file File object
     * @param string $url File URL
     * @return array
     */
    public static function file($file, $url)
    {
        return [
            'id' => $file->id,
            'fileName' => $file->fileName,
            'comment' => $file->comment,
            'type' => $file->type,
            'fileUrl' => $url
        ];
    }

    /**
     * Notification response message
     * @param Notification $notification Notification object
     * @param string $message Notification message
     * @return array
     */
    public static function notification($notification, $message)
    {
        return [
            'id' => $notification->id,
            'message' => $message,
            'viewStatus' => $notification->viewStatus,
            'createdAt' => $notification->createdAt
        ];
    }

    /**
     * Review request
     * @param reviewRequest $reviewRequest Review request object
     * @param array $requester Requester details
     * @param array $receiver Receiver details
     * @return array
     */
    public static function reviewRequest($reviewRequest, $requester, $receiver)
    {
        return [
            'id' => $reviewRequest->id,
            'requesterUserId' => $requester['id'],
            'receiverUserId' => $receiver['id'],
            'createdAt' => $reviewRequest->createdAt,
            'requester' => $requester,
            'receiver' => $receiver
        ];
    }

    /**
     * User review
     * @param UserReview $userReview User review object
     * @param array $reviewedUser Reviewed user details
     * @return array
     */
    public static function userReview($userReview, $reviewedUser)
    {
        return [
            'id' => $userReview->id,
            'userId' => $userReview->userId,
            'rating' => $userReview->rating,
            'title' => $userReview->title,
            'comment' => $userReview->comment,
            'createdAt' => $userReview->createdAt,
            'reviewedUser' => $reviewedUser
        ];
    }

    /**
     * S3 File
     * @param string $fileName Name of the file
     * @param string $url File URL
     * @param string $thumbnailName Thumbnail name
     * @param string $thumbnailUrl Thumbnail URL
     * @return array
     */
    public static function s3FileInfo($fileName, $url, $thumbnailName = '', $thumbnailUrl = '')
    {
        return [
            'fileName' => $fileName,
            'url' => $url,
            'thumbnailName' => $thumbnailName,
            'thumbnailUrl' => $thumbnailUrl
        ];
    }

    /**
     * Payment Plan
     * @param PaymentPlan $paymentPlan PaymentPlan object
     * @return array
     */
    public static function paymentPlan($paymentPlan)
    {
        return [
            'id' => $paymentPlan->id,
            'expire' => date('Y-m', strtotime($paymentPlan->expire)),
            'cardType' => $paymentPlan->cardType,
            'cardNumber' => $paymentPlan->cardNumber,
            'adyenPspReference' => $paymentPlan->adyenPspReference,
            'adyenShopperReference' => $paymentPlan->adyenShopperReference,
            'paymentGateway' => $paymentPlan->paymentGateway,
            'cardHolderName' => $paymentPlan->cardHolderName
        ];
    }

    /**
     * Statistic
     * @param array $incomeSummary Income summary of owner
     * @param array $incomeHistory Income history of owner
     * @return array
     */
    public static function statistic($incomeSummary, $incomeHistory)
    {
        return [
            'incomeSummary' => $incomeSummary,
            'incomeHistory' => $incomeHistory
        ];
    }

    /**
     * Adyen merchant signature
     * @param array $signature Signature string
     * @return array
     */
    public static function adyenMerchantSig($signature)
    {
        return [
            'signature' => $signature
        ];
    }

    /**
     * Application settings
     * @param float $commission Default commission
     * @return array
     */
    public static function appSetting($commission)
    {
        return [
            'commission' => $commission
        ];
    }

    /**
     * MangoPay related form details
     * @param array $incomeRangeList Possible income ranges defined by MangoPay
     * @param array $countryOfResList Residential country list defined by MangoPay
     * @param array $nationalityList Nationalities defined by MangoPay
     * @return array
     */
    public static function mpFormInfo($incomeRangeList, $countryOfResList, $nationalityList)
    {
        return [
            'incomeRangeList' => $incomeRangeList,
            'countryOfResList' => $countryOfResList,
            'nationalityList' => $nationalityList,
        ];
    }

    /**
     * User's MangoPay account details
     * @param UserMpInfo $userMpInfo UserMpInfo object
     * @return array
     */
    public static function userMpInfo($userMpInfo)
    {
        return [
            'id' => $userMpInfo->id,
            'address' => $userMpInfo->address,
            'nationality' => $userMpInfo->nationality,
            'countryOfResidence' => $userMpInfo->countryOfResidence,
            'email' => $userMpInfo->email,
            'firstName' => $userMpInfo->firstName,
            'lastName' => $userMpInfo->lastName,
            'birthDate' => $userMpInfo->birthDate,
            'incomeRange' => $userMpInfo->incomeRange,
            'occupation' => $userMpInfo->occupation,
            'iban' => $userMpInfo->iban,
            'city' => $userMpInfo->city,
            'postalCode' => $userMpInfo->postalCode
        ];
    }

    /**
     * User's MangoPay file details
     * @param UserMpInfoFile $userMpInfoFile UserMpInfoFile object
     * @return array
     */
    public static function userMpInfoFile($userMpInfoFile)
    {
        return [
            'id' => $userMpInfoFile->id,
            'userMpInfoId' => $userMpInfoFile->userMpInfoId,
            'userId' => $userMpInfoFile->userId,
            'fileName' => $userMpInfoFile->fileName,
            'fileUrl' => $userMpInfoFile->getFileUrl(),
            'type' => $userMpInfoFile->type,
            'status' => $userMpInfoFile->status,
            'createdAt' => $userMpInfoFile->createdAt
        ];
    }
}
?>