<?php
/************ Create User *********/
 
/**
 * @api {post} http://<base-url>/api/user Create User
 * @apiDescription Add new user to the system. Use this method for user registration. 
 * - Refer "User Object Details" for necessary parameters. Following example illustrates the valid parameters for user create.
 * Rest of the parameters described in "User Object Details" are used for viewing and some other requests.
 * - Success response - "Common Response", possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, EMAIL_EXISTS, PHONE_EXISTS, VALIDATION_FAILED, INVALID_EMAIL
 *
 * @apiName Create
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *        "firstName": "Yohan",
 *        "lastName": "Piyadigamage",
 *        "password": "test.123",
 *        "email": "yohan@gmail.com",
 *        "type": 1,
 *        "fbId": "",
 *        "fbAccessToken": "",
 *        "linkedInId": "",
 *        "linkedInAccessToken": "",
 *        "gplusId": "",
 *        "gplusAccessToken": "",
 *        "phone": "+94773959671",
 *        "bankAccountNo": "2564",
 *        "bankName": "HNB",
 *        "profileImage":"prof_pic_256.jpg",
 *        "profileImageThumb":"prof_pic_thumb_256.jpg",
 *        "files": [
 *            {
 *                "fileName": "tax_5_153654.jpg",
 *                "comment": "testfile",
 *                "type": 1
 *            },
 *            {
 *                "fileName": "tax_4_15dd3654.jpg",
 *                "comment": "testfilesss",
 *                "type": 1
 *            }
 *        ],
 *        "dob":"2005-10-15",
 *        "iban":"2564",
 *        "swift":"4589",
 *        "bankAccountName":"Yohan D.H",
 *        "language":"en-US",
 *        "profDes":"I think I am the best :)",
 *        "companyRegNum":"",
 *        "companyType":1,
 *        "companyName":"Yohan & Sons"
 *    }
 */
 
 
/************ Get User *********/
 
/**
 * @api {get} http://<base-url>/api/user/:id Get User
 * @apiDescription Retrieve existing user details. 
 * - Success response - "User Object"
 * - Failed response - "Common Response", possible status codes are RECORD_NOT_EXISTS.
 * - access-token required in header.
 *
 * @apiName GetUser
 * @apiGroup User
 *
 * @apiParam {number} id User id.
 *
 * @apiSuccessExample Success-Response:
 *    {
 *        "firstName": "Yohan",
 *        "lastName": "Piyadigamage",
 *        "email": "yohan@gmail.com",
 *        "type": 1,
 *        "fbId": "",
 *        "fbAccessToken": "",
 *        "linkedInId": "",
 *        "linkedInAccessToken": "",
 *        "gplusId": "",
 *        "gplusAccessToken": "",
 *        "phone": "+94773959671",
 *        "bankAccountNo": "2564",
 *        "bankName": "HNB",
 *        "profileImage":"prof_pic_256.jpg",
 *        "profileImageThumb":"prof_pic_thumb_256.jpg",
 *        "files": [
 *            {
 *                "fileName": "tax_5_153654.jpg",
 *                "comment": "testfile",
 *                "type": 1
 *            },
 *            {
 *                "fileName": "tax_4_15dd3654.jpg",
 *                "comment": "testfilesss",
 *                "type": 1
 *            }
 *        ],
 *        "dob":"2005-10-15",
 *        "iban":"2564",
 *        "swift":"4589",
 *        "bankAccountName":"Yohan D.H",
 *        "language":"en-US",
 *        "profDes":"I think I am the best :)",
 *        "companyRegNum":"",
 *        "companyType":1,
 *        "language":"en-US"
 *    }
 *
 * @apiExample Example Request:
 * /user/2
 *
 */

/************ Update User *********/
 
/**
 * @api {put} http://<base-url>/api/user/:id Update User
 * @apiDescription Update existing user details. 
 * - Refer "User Object Details" for necessary parameters. Following example illustrates the valid parameters for user update.
 * - Success response - "Common Response", possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, EMAIL_EXISTS, PHONE_EXISTS, VALIDATION_FAILED, INVALID_EMAIL.
 * - access-token required in header.
 *
 * @apiName Update
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *        "firstName": "Yohan",
 *        "lastName": "Piyadigamage",
 *        "password": "test.123",
 *        "email": "yohan@gmail.com",
 *        "type": 1,
 *        "fbId": "",
 *        "fbAccessToken": "",
 *        "linkedInId": "",
 *        "linkedInAccessToken": "",
 *        "gplusId": "",
 *        "gplusAccessToken": "",
 *        "phone": "+94773959671",
 *        "bankAccountNo": "2564",
 *        "bankName": "HNB",
 *        "profileImage":"prof_pic_256.jpg",
 *        "profileImageThumb":"prof_pic_thumb_256.jpg",
 *        "files": [
 *            {
 *                "fileName": "tax_5_153654.jpg",
 *                "comment": "testfile",
 *                "type": 1
 *            },
 *            {
 *                "fileName": "tax_4_15dd3654.jpg",
 *                "comment": "testfilesss",
 *                "type": 1
 *            }
 *        ],
 *        "dob":"2005-10-15",
 *        "iban":"2564",
 *        "swift":"4589",
 *        "bankAccountName":"Yohan D.H",
 *        "language":"en-US",
 *        "profDes":"I think I am the best :)",
 *        "companyRegNum":"",
 *        "companyType":1
 *    }
 */
 
 /************ Authenticate User *********/
 
 /**
 * @api {post} http://<base-url>/api/user/authenticate Authenticate User
 * @apiDescription Authenticate user. 
 * - Refer "User Authenticate Object" for request parameters.
 * - Success response - "Authentication Response". Possible status codes are SUCCESS, FAILED
 * @apiName Authenticate User
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "email": "gamunu@gmail.com",
 *       "password": "Z2FtdW51LjEyMw==",
 *       "loginType": 1,
 *    }
 *
 */
 
/************ Change Password *********/
 
 /**
 * @api {put} http://<base-url>/api/user/change-password Change user password
 * @apiDescription Change user password.
 * - Success response - "Common Response". Possible status codes are SUCCESS, FAILED, INVALID_OLD_PASSWORD.
 * - access-token required in header.
 * @apiName ChangePassword
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "password": "Z2FtdW51dddLjEyMw==",
 *       "oldPassword": "Z2FtdW51LjEyMw=="
 *    }
 *
 */
 
/************ Invite Tenant *********/
 
 /**
 * @api {post} http://<base-url>/api/user/invite-tenant Invite Tenant
 * @apiDescription Send email invitation to tenant.
 * - Refer "Invite Tenant Object" for request parameters.
 * - Success response - "Common Response". Possible status codes are SUCCESS, FAILED, INVALID_EMAIL.
 * - access-token is required in header.
 *
 * @apiName InviteTenant
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "emal": "saman@gmail.com",
 *       "message": "Test message"
 *    }
 *
 */
 
/************ My Owners List *********/
 
/**
 * @api {get} http://<base-url>/api/user/my-owners Get My Owner List
 * @apiDescription Retreive past owners of a Tenant.
 * - Success response - "List Object" containing multiple instances of "User Object".
 * - access-token is required in header.
 *
 * @apiName GetMyOwners
 * @apiGroup User
 *
 * @apiSuccessExample Success-Response:
 *     { 
 *        "total":1,
 *        "data":[ 
 *           { 
 *              "id":23,
 *              "firstName":"Aruna",
 *              "lastName":"Attanayake",
 *              "email":"aruna470@gmail.com",
 *              "type":1,
 *              "phone":"773959693",
 *              "profileImage":"test.jpg"
 *           },
 *            ...
 *            ...
 *        ]
 *     }
 *
 *
 * @apiExample Example Request:
 * api/user/my-owners
 *
 */
 
 /************ Send Verification Code *********/
 
/**
 * @api {post} http://<base-url>/api/user/send-verify-code Send Verification Code
 * @apiDescription Send verification code to user via SMS.
 * - Refer "Verify Code Object" for request parameters. No need to send the "verificationCode"
 * - Success response - "Common Response", Possible status codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, PHONE_EXISTS, INVALID_PHONE_NUMBER.
 *
 * @apiName SendVerificationCode
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "phoneNumber": "+94773959694"
 *    }
 */
 
/**
 * @api {post} http://<base-url>/api/user/verify-code Verify Code
 * @apiDescription Verify whether user entered verification code is valid.
 * - Refer "Verify Code Object" for request parameters. Need to send both the parameters.
 * - Success response - "Common Response", Possible status codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED.
 *
 * @apiName VerifyCode
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "phoneNumber": "+94773959694",
 *       "verificationCode": "4569"
 *    }
 *
 */
 
 /**
 * @api {post} http://<base-url>/api/user/forgot-password Forgot password
 * @apiDescription Send an email to particular user along with password reset link. 
 * Password reset link needs to be configured in BO. BO sends password reset link on the email as follows. 
 * When user clicks, it callbacks with "q" parameter which contains the password reset token. 
 * It needs to be sent to BO when making "Reset Password" API call.
 * - Ex:http://password/reset/url?q=askfdjaeei12 
 * - Refer "ForgotPasswordObject" for request parameters.
 * - Success response - "Common Response", Possible status codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_NOT_EXISTS.
 *
 * @apiName ForgotPassword
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "email": "yohan@gmail.com"
 *    }
 *
 */
 
/**
 * @api {post} http://<base-url>/api/user/reset-password Reset password
 * @apiDescription Reset user password. This is the second step of forgot password process. 
 * - Refer "Reset Password Object" for request parameters.
 * - Success response - "Common Response". Possible status codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_NOT_EXISTS.
 *
 * @apiName ResetPassword
 * @apiGroup User
 *
 * @apiExample Example Request:
 *    {
 *       "passwordResetToken": "56ab8eae386c023",
 *       "password": "yohan@125"
 *    }
 *
 */