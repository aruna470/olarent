<?php

/************ User Object *********/

/**
 * @api {user-object} {} User Object
 * @apiDescription User object attributes.
 * @apiName UserObject
 * @apiGroup Objects
 *
 * @apiParam {String{30}} [firstName] Firstname of the user.
 * @apiParam {String{30}} [lastName] Lastname of the user.
 * @apiParam {String{40}} [password] User password.
 * @apiParam {String{60}} email User Email.
 * @apiParam {Number} type User type. 1 - Owner, 2 - Tenant
 * @apiParam {String{30}} [fbId] Facebook id.
 * @apiParam {String{45}} [fbAccessToken] Facebook access token.
 * @apiParam {String{15}} [linkedInId] LinkedIn id.
 * @apiParam {String{45}} [linkedInAccessToken] LinkedIn access token.
 * @apiParam {String{35}} [gplusId] G+ id.
 * @apiParam {String{30}} [gplusAccessToken] G+ access token.
 * @apiParam {String{20}} [phone] User phone number in international format.
 * @apiParam {String{45}} [bankAccountNo] Bank account number.
 * @apiParam {String{30}} [bankName] Bank name of the owner.
 * @apiParam {String{255}} [profileImage] Profile image name or URL(Social profile picture URL).
 * @apiParam {String{255}} [profileImageOrig] Profile image name or URL as it is, without converting them to S3 format. Assign this value to profileImage attribute.
 * @apiParam {String{40}} [profileImageThumb] Profile thumbnail image name.
 * @apiParam {Object[]} [files] Multiple file objects. Refer "FileObjectDetails".
 * @apiParam {String{10}} [dob] Date of birth. Format:yyyy-mm-dd.
 * @apiParam {String{60}} [iban] IBAN.
 * @apiParam {String{10}} [swift] SWIFT code.
 * @apiParam {String{10}} [bankAccountName] Bank account name.
 * @apiParam {Number} [rating] Average user rating.
 * @apiParam {Number} [isRequestedForReview] Whether requesting user already sent a review request to this user. 1 - Yes, 2 - No. Use in My Owners.
 * @apiParam {String{8}} [language] User preferred language. en-Us or fr-FR
 * @apiParam {String} [profDes] Profile description
 * @apiParam {String{15}} [companyRegNum] Company registration number of rental agency.
 * @apiParam {Number} [companyType] Company types. 0 - Personal, 1 - Real state agency, 2 - Property management, 3 - Building management
 * @apiParam {String{30}} [companyName] Name of the company.
 */

 
/************ File Object *********/

/**
 * @api {file-object} {} File Object
 * @apiDescription File object attributes.
 * @apiName FileObject
 * @apiGroup Objects
 *
 * @apiParam {String{30}} fileName File name. Tax file – tax_timestamp.ext, Income proof – ip_timestamp.ext, Bank guarantee – bg_timestamp.ext, Co-Signer – cos_timestamp.ext, Caution solidare – cs_timestamp.ext, ID – id_timestamp.ext
 * @apiParam {String{64}} [comment] Comment.
 * @apiParam {Number} type File type. 1 - Tax file, 2 - Income proof, 3 - Bank Guarantee, 4 - Caution Solidare, 5 - Co-Signer, 6 - ID
 * @apiParam {String} [fileUrl] File accessible URL(S3 URL).
 */

/************ User Authenticate Object *********/

/**
 * @api {user-authenticate-object} {} User authenticate Object
 * @apiDescription User authenticate object attributes.
 * @apiName UserAuthenticateObject
 * @apiGroup Objects
 *
 * @apiParam {String{60}} [email] User email address. Required if login type is 1
 * @apiParam {String{40}} [password] User password. Base64 encoded
 * @apiParam {Number} loginType User login type. 1 - Email, 2 - Facebook, 3 - LinkedIn, 4 - G+ 
 * @apiParam {String{30}} [fbId] Facebook id. Required if login type is 2
 * @apiParam {String{15}} [linkedInId] LinkedIn id. Required if login type is 3
 * @apiParam {String{35}} [gplusId] G+ id. Required if login type is 4
 */
 
/************ Change Password Object *********/

/**
 * @api {change-password-object} {} Change Password Object
 * @apiDescription Change Password Object attributes.
 * @apiName ChangePasswordObject
 * @apiGroup Objects
 *
 * @apiParam {String{40}} [password] New password. Base64 encoded
 * @apiParam {String{40}} [oldPassword] Current password. Base64 encoded
 */
 
/************ Invite Tenant Object *********/

/**
 * @api {invite-tenant-object} {} Invite Tenant Object
 * @apiDescription Invite Tenant.
 * @apiName InviteTenantObject
 * @apiGroup Objects
 *
 * @apiParam {String} message Message to be delivered
 * @apiParam {String} email Invitee email
 */
 
/************ List Object *********/

/**
 * @api {list-object} {} List Object
 * @apiDescription Contains multiple API query results.
 * @apiName ListObject
 * @apiGroup Objects
 *
 * @apiParam {Number} total Total number of records matched with particular query. Not the number of results on current response.
 * @apiParam {Object[]} data Multiple instance of particular object.
 */
 
/************ Verify Code Object *********/

/**
 * @api {verify-code-object} {} Verify Code Object
 * @apiDescription Verify mobile number via SMS.
 * @apiName VerifyObject
 * @apiGroup Objects
 *
 * @apiParam {String{30}} phoneNumber Mobile number in international format.
 * @apiParam {String} [verificationCode] Verification code.
 */
 
/************ Forgot Password Object *********/

/**
 * @api {forgot-password-object} {} Forgot Password Object
 * @apiDescription Forgot password.
 * @apiName ForgotPasswordObject
 * @apiGroup Objects
 *
 * @apiParam {String{64}} email User email.
 */
 
/************ Reset Password Object *********/

/**
 * @api {reset-password-object} {} Reset Password Object
 * @apiDescription Reset password.
 * @apiName ResetPasswordObject
 * @apiGroup Objects
 *
 * @apiParam {String} passwordResetToken Password reset token which comes with password change URL.
 * @apiParam {String} password New password.
 */
 
 

 /************ Property Object *********/
 
 /**
 * @api {property-object} {} Property Object
 * @apiDescription Property Details.
 * @apiName PropertyObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Property id.
 * @apiParam {Object} [owner] User object of Owner.
 * @apiParam {Object} [tenant] User object of Tenant.
 * @apiParam {String{11}} [code] 6 digit property code.
 * @apiParam {String{150}} [name] Property title.
 * @apiParam {String} [description] Property description.
 * @apiParam {String{90}} [address] Property address.
 * @apiParam {String{25}} [city] City.
 * @apiParam {Number} [cost] Property cost.
 * @apiParam {Number} [status] Property availability. 1 - Available, 2 - Unavailable.
 * @apiParam {String{30}} [imageName] Image Name. Format:prop_<user_id>_<timestamp>.ext.
 * @apiParam {String} [imageUrl] Full URL of the image. Can be S3 bucket URL or any other URL.
 * @apiParam {String{40}} [thumbImageName] Thumbnail image name. Format:thumb_<timestamp>.ext.
 * @apiParam {String} [thumbImageUrl] Full URL of the image. Can be S3 bucket URL or any other URL.
 * @apiParam {String} [currentRentDueAt] Current rental due date. Format:yyyy-mm-dd
 * @apiParam {String{12}} [zipCode] ZIP coe.
 * @apiParam {Number} [noOfRooms] Number of rooms.
 * @apiParam {Number} [size] Size of the property (Square feet).
 * @apiParam {Number} [keyMoney] Deposit amount.
 * @apiParam {Number} [lastPaymentStatus] Payment status. 1-success,2-failed,3-pending,4-not rented.
 * @apiParam {String} [paymentDueAt] Next payment date.
 * @apiParam {String} [paymentDate] Payment date & time in UTC. Format yyyy-mm-dd hh:mm:ss.
 * @apiParam {Number} [isEditable] Whether record is editable or not.1 - Editable, 0 - Not editable.
 * @apiParam {Number} [totalPendingPayments] Total pending payments.
 * @apiParam {Number} [payDay] Payment date of the month.
 * @apiParam {Number} [payNowEnable] Enable/Disable pay now button.
 * @apiParam {Number} [commissionPlan] Commision plan identifier. 1 – by renter, 2 – by owner, 3 - split.
 * @apiParam {Number} [duration] Property rent out duration in months.
 * @apiParam {Number} [payKeyMoney] Whether to pay keymoney via CC. 1 – Yes, 0 - No.
 * @apiParam {Object[]} [images] Multiple image objects.
 * @apiParam {String} [image.imageName] Image name.
 * @apiParam {String} [image.imageUrl] image URL. No need to send image URL when creating or updating the record. It contains the S3 URL of the image when viewing.
 * @apiParam {String} [image.thumbImageName] Thumbnail image name.
 * @apiParam {String} [image.thumbImageUrl] Thumbnail image URL. No need to send thumbImage URL when creating or updating the record. It contains the S3 URL of the image when viewing.
 * @apiParam {Number} [image.isDefault] This will be the default display image. 1-yes,0-no.
 */

 
/************ Property Request Object *********/
 
/**
 * @api {property-request-object} {} Property Request Object
 * @apiDescription Property Request Details.
 * @apiName PropertyRequestObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Property request id.
 * @apiParam {String} [code] Property code.
 * @apiParam {Number} [tenantUserId] User id of the Tenant.
 * @apiParam {Number} [payDay] Paying day of the month. Between 1-30
 * @apiParam {Number} [bookingDuration] Booking duration in months.
 * @apiParam {Number} [status] Status of the request. 0 – Pending, 1 – Accepted, 2 - Rejected.
 * @apiParam {Number} [payKeyMoneyCc] Pay key money via CC. 0 – No, 1 – Yes.
 * @apiParam {Object} [owner] Refer "UserObject".
 * @apiParam {Object} [tenant] Refer "UserObject".
 * @apiParam {Object} [property] Refer "PropertyObject".
 */

 
/************ Notification Object *********/
 
/**
 * @api {notification-object} {} Notification Object
 * @apiDescription Notification Details.
 * @apiName NotificationObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Notification id.
 * @apiParam {String} [message] Notification description.
 * @apiParam {Number} [viewStatus] Wheather user has viewed or not. 0 - pending, 1 - viewed.
 * @apiParam {String} [createdAt] Notification date & time
 */
 
/************ Review Request Object *********/
 
/**
 * @api {review-request-object} {} Review Request Object
 * @apiDescription Review request.
 * @apiName ReviewRequestObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Review request id.
 * @apiParam {Number} [requesterUserId] User id of the requester (Tenant).
 * @apiParam {Number} [receiverUserId] User id of the receiver (Owner).
 * @apiParam {String} [createdAt] Requested date & time
 * @apiParam {Object} [requester] Refer "User Object"
 * @apiParam {Object} [receiver] Refer "User Object"
 */
 
/************ User Review Object *********/
 
/**
 * @api {user-review-object} {} User Review Object
 * @apiDescription User review.
 * @apiName UserReviewObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Review request id.
 * @apiParam {Number} [userId] Id of the user who has requested for a review.
 * @apiParam {Number} [rating] User rating. min - 1, max - 5.
 * @apiParam {String{45}} [title] Review title.
 * @apiParam {String{145}} [comment] Description entered by the reviewer".
 * @apiParam {String} [createdAt] Review created date & time.
 * @apiParam {Number} [reviewRequestId] Associated review request id.
 * @apiParam {Object} [reviewedUser] Refer "User Object".
 */
 
/************ S3 File Object *********/
 
/**
 * @api {S3-file-object} {} S3 File Object
 * @apiDescription S3 File Management.
 * @apiName S3FileObject
 * @apiGroup Objects
 *
 * @apiParam {Object} [file] File field.
 * @apiParam {String} [fileData] File content as base64 encoded string. Either file or fileData need to be present.
 * @apiParam {String} [fileName ] File name.
 * @apiParam {String} [options] Additional parameters. Should be a JSON string. 
 *  - {"compress":1, "thumbnail":1, "thumbnailWidth":40}
 *  - compress - Whether need to reduce the size of original image
 *  - thumbnail - Whether need to generate thumbnail. 1 – yes, 0 – no
 *  - thumbnailWidth - Thumbnail width in pixels
 * @apiParam {String} [s3Options] Any other S3 file uploading attributes defined here 
 * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject except Bucket, 
 * Key and Body parameters. Should be a JSON string ex. {"ACL":"public-read"}
 * @apiParam {String} [url] File URL
 * @apiParam {String} [thumbnailName] Thumbnail name
 * @apiParam {String} [thumbnailUrl] Thumbnail URL
 */
 
/************ Statics Object *********/

/**
 * @api {statics-object} {} Statistic Object
 * @apiDescription Statistic details.
 * @apiName StaticObject
 * @apiGroup Objects
 *
 * @apiParam {Object} [incomeSummary] Pending and received payments of current month.
 * @apiParam {Number} [incomeSummary.pending] Total pending payments of current month.
 * @apiParam {Number} [incomeSummary.received] Total received payments of current month.
 * @apiParam {Object[]} [incomeHistory] Income of each month(last 12 months).
 * @apiParam {String} [incomeHistory.month] Name of the month.Ex: JAN,FEB.
 * @apiParam {Number} [incomeHistory.income] Total received payments of particular month.
 */
 
/************ Payment Plan Object *********/

/**
 * @api {statics-object} {} Payment Plan Object
 * @apiDescription Payment Plan.
 * @apiName PaymentPlanObject
 * @apiGroup Objects
 *
 * @apiParam {Number} [id] Record id.
 * @apiParam {String} [expire] Card expire date. Format: yyyy-mm.
 * @apiParam {String} [cardType] Paying card type. Ex: visa|mastercard. Required if gateway is 1
 * @apiParam {String} [cardNumber] Last 4 digits of the card.
 * @apiParam {String} [adyenPspReference] Psp reference value return from Adyen API after creating a recurring contract. Required if gateway is 1
 * @apiParam {String} [adyenShopperReference] Shopper reference that sent while creating a recurring contract. Required if gateway is 1
 * @apiParam {Number} paymentGateway Payment gateway. 1 – Adyen, 2 - Stripe.
 * @apiParam {Number} [amount] Initial payment that submit when adding the card. This amount will be refunded..
 * @apiParam {String} [currency] Currency format.Ex:USD/EUR.
 * @apiParam {String} [stripeToken] Stripe card token. Required if gateway is 2
 * @apiParam {String} [cardHolderName] Name of the card holder.
 */


/************ MangoPay FormInfo Object *********/

/**
 * @api {mangopay-form-info-object} {} MangoPay Form Info Object
 * @apiDescription MangoPay form info object attributes.
 * @apiName MangoPayFormInfoObject
 * @apiGroup Objects
 *
 * @apiParam {Object} incomeRangeList Income range list
 * @apiParam {Object} countryOfResList Country of residence list
 * @apiParam {Object} nationalityList Nationality list
 */

/************ User MangoPayInfo Object *********/

/**
 * @api {user-mp-info-object} {} User Mp Info Object
 * @apiDescription User's MangoPay Payout related information
 * @apiName UserMpInfoObject
 * @apiGroup Objects
 *
 * @apiParam {String{255}} address  Address
 * @apiParam {String{3}} nationality Nationality. Use "GetMangoPayFormInfo" method to get available nationalities.
 * @apiParam {String{3}} countryOfResidence User's residential country. Use "GetMangoPayFormInfo" method to get available countries.
 * @apiParam {String{60}} email  Email
 * @apiParam {String{60}} firstName  First name
 * @apiParam {String{60}} lastName  Last name
 * @apiParam {String{10}} birthDate  Date of birth. Format:yyyy-mm-dd
 * @apiParam {String} incomeRange  User's annual income range. Use "GetMangoPayFormInfo" method to get available income ranges.
 * @apiParam {String{60}} occupation  Occupation
 * @apiParam {String{60}} iban  IBAN. Should be a real one.
 * @apiParam {String{60}} occupation  Occupation
 * @apiParam {String{60}} city  City
 * @apiParam {String{10}} postalCode  Postal code. Alphanumeric
 */

/************ User MangoPayInfoFile Object *********/

/**
 * @api {user-mp-info-file-object} {} User Mp Info File Object
 * @apiDescription User's MangoPay related files, Such as identity proof
 * @apiName UserMpInfoFileObject
 * @apiGroup Objects
 *
 * @apiParam {Number} id Object id
 * @apiParam {Number} userMpInfoId User`s MangoPayInfo object id.
 * @apiParam {Number} userId User id.
 * @apiParam {String{30}} fileName  File name
 * @apiParam {Number} type  File type. 1 - Identity proof
 * @apiParam {Number} status File approval status. 1 - pending, 2 - success, 3 - failed
 * @apiParam {String} createdAt created date & time. Format:yyyy-mm-dd
 */