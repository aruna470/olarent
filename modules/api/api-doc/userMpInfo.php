<?php

/************ Get MangoPay Form Info *********/

/**
 * @api {get} http://<base-url>/api/user-mp-info/get-mp-form-info Get MangoPay Form Info
 * @apiDescription Retrieve form details related to MangoPay
 * - Success response - "User Mp Info Object".
 * - access-token is required in header.
 *
 * @apiName GetMangoPayFormInfo
 * @apiGroup UserMpInfo
 *
 * @apiSuccessExample Success-Response:
 *     {
 *         "incomeRangeList": {
 *             "1": "Less than 18K €",
 *             "2": "Between 18 and 30K €",
 *             "3": "Between 30 and 50K €",
 *             "4": "Between 50 and 80K €",
 *             "5": "Between 80 and 120K €",
 *             "6": "Greater than 120K €"
 *         },
 *         "countryOfResList": {
 *             "FR": "France"
 *         },
 *         "nationalityList": {
 *             "FR": "France"
 *         }
 *     }
 */

/************ Create User MangoPay Info *********/

/**
 * @api {post} http://<base-url>/api/user-mp-info Create MangoPay Account
 * @apiDescription Create MangoPay account for payouts. You can have only one account per user.
 * - Refer "User Mp Info Object" for parameter details. Following example illustrates the valid parameters for create.
 * Rest of the parameters described in "User Mp Info Object" are used for viewing and some other requests.
 * - Mandatory fields are address, nationality, counryOfResidence, email, firstName, lastName, birthDate, incomeRange, occupation, iban.
 * - Success response - "Common Response" Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_EXISTS
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup UserMpInfo
 *
 * @apiExample Example Request:
 *    {
 *        "address":"test",
 *        "nationality":"FR",
 *        "countryOfResidence":"FR",
 *        "email":"aruna470@gmail.com",
 *        "firstName":"Aruna",
 *        "lastName":"Attanayake",
 *        "birthDate":"1981-10-06",
 *        "incomeRange":"1",
 *        "occupation":"test",
 *        "iban":"FR1420041010050500013M02606"
 *    }
 *
 * @apiExample Example Response:
 *    {
 *        "code":"SUCCESS",
 *        "message":null,
 *        "extraParams":{
 *            "userMpInfoId":3
 *        }
 *    }
 */

/************ Update MangoPay Info *********/

/**
 * @api {put} http://<base-url>/api/user-mp-info/:id Update MangoPay Account
 * @apiDescription Update MangoPay account details.
 * - Refer "User Mp Info Object" for parameter details. Following example illustrates the valid parameters for update.
 * Rest of the parameters described in "User Mp Info Object" are used for viewing and some other requests.
 * - Success response - "Common Response", possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_NOT_EXISTS
 * - access-token is required in header.
 *
 * @apiName Update
 * @apiGroup UserMpInfo
 *
 * @apiExample Example Request:
 *    {
 *        "address":"test",
 *        "nationality":"FR",
 *        "countryOfResidence":"FR",
 *        "email":"aruna470@gmail.com",
 *        "firstName":"Aruna",
 *        "lastName":"Attanayake",
 *        "birthDate":"1981-10-06",
 *        "incomeRange":"1",
 *        "occupation":"test",
 *        "iban":"FR7630004005520001011817204"
 *    }
 *
 * @apiExample Example Response:
 *    {
 *        "code":"SUCCESS",
 *        "message":null,
 *        "extraParams":{
 *            "userMpInfoId":3
 *        }
 *    }
 **/


/************ Get MangoPay Info *********/

/**
 * @api {get} http://<base-url>/api/user-mp-info/ Get MangoPay Account
 * @apiDescription Retrieve existing MangoPay account details.
 * - Success response - "User Mp Info Object"
 * - Failed response - "Common Response". Possible status codes are RECORD_NOT_EXISTS.
 * - access-token required in header.
 *
 * @apiName GetUserMpInfo
 * @apiGroup UserMpInfo
 *
 * @apiExample Example Response:
 *    {
 *        "address":"test",
 *        "nationality":"FR",
 *        "countryOfResidence":"FR",
 *        "email":"aruna470@gmail.com",
 *        "firstName":"Aruna",
 *        "lastName":"Attanayake",
 *        "birthDate":"1981-10-06",
 *        "incomeRange":"1",
 *        "occupation":"test",
 *        "iban":"FR7630004005520001011817204"
 *    }
 */

/************ Create User MangoPay Info File *********/

/**
 * @api {post} http://<base-url>/api/user-mp-info/create-file Create MangoPay File
 * @apiDescription Upload proof documents
 * - Refer "User Mp Info File Object" for parameter details. Following example illustrates the valid parameters for create.
 * Rest of the parameters described in "User Mp Info Object File" are used for viewing and some other requests.
 * - Mandatory fields are fileName, type.
 * - Success response - "Common Response" Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token required in header.
 *
 * @apiName CreateFile
 * @apiGroup UserMpInfo
 *
 * @apiExample Example Request:
 *    {
 *        "fileName":"kyc.jpg",
 *        "type":1
 *    }
 */


/************ Get MangoPay Files *********/

/**
 * @api {get} http://<base-url>/api/user-mp-info/get-files Get MangoPay Files
 * @apiDescription Retrieve list of uploaded files.
 * - Success response - Multiple instances of "User Mp Info File Object".
 * - access-token required in header.
 *
 * @apiName GetFiles
 * @apiGroup UserMpInfo
 *
 * @apiExample Example Response:
 *    [
 *       {
 *           "id": 1,
 *           "userMpInfoId": 2,
 *           "userId": 23,
 *           "fileName": "kyc.jpg",
 *           "fileUrl": "https://s3.amazonaws.com/olarent-user-files1/kyc.jpg?.. *    ",
 *           "type": 1,
 *           "status": 1,
 *           "createdAt": "2016-05-29 06:12:34"
 *       }
 *    ]
 */