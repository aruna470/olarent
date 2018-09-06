<?php
/************ Create Property *********/
 
/**
 * @api {post} http://<base-url>/api/property Create Property
 * @apiDescription Add new property 
 * - Refer "PropertyObjectDetails" for parameter details. Following example illustrates the valid parameters for property create.
 * Rest of the parameters described in "PropertyObjectDetails" are used for viewing and some other requests.
 * - Mandatory fields are name, description, address, cost.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup Property
 *
 * @apiExample Example Request:
 *     { 
 *        "description":"Testrr",
 *        "name":"Testdd",
 *        "address":"Colombo",
 *        "city":"Colombo",
 *        "cost":"15",
 *        "size":1000,
 *        "noOfRooms":2,
 *        "zipCode":"6254",
 *        "imageName":"45682.jpg",
 *        "thumbImageName":"thumb_45682.jpg",
 *        "images":[ 
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":0
 *           },
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":1
 *           }
 *        ]
 *     }
 */
 
/************ Update Property *********/
 
/**
 * @api {put} http://<base-url>/api/property/:id Update Property
 * @apiDescription Update property. You are allowed to update only not rented properties. 
 * - Refer "PropertyObjectDetails" for parameter details. Following example illustrates the valid parameters for property update.
 * Rest of the parameters described in "PropertyObjectDetails" are used for viewing and some other requests.
 * - Success response - "Common Response", possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, PROPERTY_UPDATE_NOT_ALLOWED
 * - access-token is required in header.
 *
 * @apiName Update
 * @apiGroup Property
 *
 * @apiExample Example Request:
 *     { 
 *        "description":"Testrr",
 *        "name":"Testdd",
 *        "address":"Colombo",
 *        "city":"Colombo",
 *        "cost":"15",
 *        "size":1000,
 *        "noOfRooms":2,
 *        "zipCode":"6254",
 *        "imageName":"45682.jpg",
 *        "thumbImageName":"thumb_45682.jpg",
 *        "images":[ 
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":0
 *           },
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":1
 *           }
 *        ]
 *     }
 */
 
 /************ Get Property *********/
 
/**
 * @api {get} http://<base-url>/api/property/:id Get Property
 * @apiDescription Retrieve existing property details. 
 * - Success response - "Property Object" 
 * - Failed response - "Common Response".Possible status codes are RECORD_NOT_EXISTS. 
 * - access-token required in header.
 *
 * @apiName GetProperty
 * @apiGroup Property
 *
 * @apiParam {number} id Property id.
 *
 * @apiExample Example Response:
 *     { 
 *        "id":1,
 *        "description":"Testrr",
 *        "name":"Testdd",
 *        "address":"Colombo",
 *        "city":"Colombo",
 *        "cost":"15",
 *        "size":1000,
 *        "noOfRooms":2,
 *        "zipCode":"6254",
 *        "imageName":"45682.jpg",
 *        "thumbImageName":"thumb_45682.jpg",
 *        "owner": {
 *            "id": 6,
 *            "firstName": "Yohan",
 *            "lastName": "Hirimuthugoda",
 *            "email": "yohan@gmail.com",
 *            "type": 1,
 *            "phone": "773959693",
 *            "profileImage":"test.jpg"
 *        },
 *        "tenant": {},
 *        "images":[
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":0
 *           },
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":1
 *           }
 *        ]
 *     }
 */
 
/************ Get Property List *********/
 
/**
 * @api {get} http://<base-url>/api/properties Get Property List
 * @apiDescription Search properties by various parameters.
 * - Success response - "List Object" containing multiple instances of "Property Object".
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetProperties
 * @apiGroup Property
 *
 * @apiParam {String} [code] Property code.
 * @apiParam {Number} [ownerUserId] User id of the owner.
 * @apiParam {Number} [tenantUserId] User id of the tenant.
 * @apiParam {Number} [status] Property availability status. Refer "Property Object" for possible statuses.
 * @apiParam {String} [ownerName] Name of the owner.
 * @apiParam {String} [smartSearchParams] Owner name, property code or city.
 * When smartSearchParams is present in the query string it applies "or" filters on code, ownername and city fields. 
 * It will not consider any query attributes other than property status.
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     {
 *         "count": "1",
 *         "data": [
 *             {
 *                 "id": 1,
 *                 "owner": {
 *                     "id": 6,
 *                     "firstName": "Yohan",
 *                     "lastName": "Hirimuthugoda",
 *                     "email": "yohan@gmail.com",
 *                     "type": 1,
 *                     "phone": "773959693",
 *                     "profileImage":"test.jpg"
 *                 },
 *                 "tenant": [],
 *                 "code": "E636B6",
 *                 "name": "Blue property",
 *                 "description": "Blue property",
 *                 "address": "Mahiyanganaya",
 *                 "cost": 25,
 *                 "status": 1,
 *                 "imageName": null,
 *                 "currentRentDueAt": null
 *             }
 *         ]
 *     }
 *
 *
 * @apiExample Example Request:
 * api/properties?page=1&limit=1&code=E636B4
 *
 */
 
/************ Delete Property *********/
 
/**
 * @api {delete} http://<base-url>/api/property/:id Delete property
 * @apiDescription Delete property. You are allowed to delete only properties those not related with other entities.
 * - Success response - "Common Response", possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS, PROPERTY_IN_USE
 * - access-token is required in header.
 *
 * @apiName DeleteProperty
 * @apiGroup Property
 *
 * @apiParam {Number} id Property id.
 *
 * @apiExample Example Request:
 * api/property/4
 *
 */

/************ Payment Details List *********/
 
/**
 * @api {get} http://<base-url>/api/property/payment-details Get Payment Details List
 * @apiDescription Property payment details.
 * - Success response - "List Object" containing multiple instances of "Property Object" 
 * with two additional attributes "lastPaymentStatus" and "paymentDate"
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetPaymentDetailsList
 * @apiGroup Property
 *
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     {
 *         "count": "1",
 *         "data": [
 *             {
 *                 "id": 1,
 *                 "owner": {
 *                     "id": 6,
 *                     "firstName": "Yohan",
 *                     "lastName": "Hirimuthugoda",
 *                     "email": "yohan@gmail.com",
 *                     "type": 1,
 *                     "phone": "773959693",
 *                     "profileImage":"test.jpg"
 *                 },
 *                 "tenant": {},
 *                 "code": "E636B6",
 *                 "name": "Blue property",
 *                 "description": "Blue property",
 *                 "address": "Mahiyanganaya",
 *                 "cost": 25,
 *                 "status": 1,
 *                 "imageName": null,
 *                 "currentRentDueAt": null,
 *                 "lastPaymentStatus":1,
 *                 "paymentDate":null
 *             }
 *         ]
 *     }
 *
 *
 * @apiExample Example Request:
 * api/property/payment-details?page=1&limit=10
 */
 
/************ Property Terminate *********/
 
 /**
 * @api {get} http://<base-url>/api/property/terminate/:id Tereminate Property
 * @apiDescription Either Owner or Tenant can terminate their contract.
 * - Success response - "Common Response", possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS, PROPERTY_TERMINATE_NOT_ALLOWED
 * - access-token is required in header.
 *
 * @apiName TerminateProperty
 * @apiGroup Property
 *
 * @apiParam {Number} id Property id
 *
 * @apiExample Example Request:
 * api/property/terminate/1
 *
 */
 
/************ Pay Now *********/
 
/**
 * @api {put} http://<base-url>/api/property/pay-now/:id Pay Now
 * @apiDescription Tenant can pay all the pending payments in case of failure of recurring payments.
 * - Success response - "Common Response", possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS, PAY_NOW_NOT_ALLOWED
 * - access-token is required in header.
 *
 * @apiName PayNow
 * @apiGroup Property
 *
 * @apiParam {Number} id Property id
 *
 * @apiExample Example Request:
 * api/property/pay-now/1
 *
 */
 
/************ Get Due Payment List *********/
 
/**
 * @api {get} http://<base-url>/api/property/due-payment Get Due Payment List
 * @apiDescription Retrieve properties with pending payments to be proceeded with pay now option or retrieve on behalf of property list
 * - Success response - "List Object" containing multiple instances of "Property Object" 
 * with three additional attributes "totalPendingPayments", "payDay" and "payNowEnable"
 * - access-token is required in header.
 *
 * @apiName GetDuePaymentList
 * @apiGroup Property
 *
 * @apiParam {Number} [isOnBhf] If you want to retrieve oh behalf of property list just pass "isOnBhf=1"
 *
 * @apiSuccessExample Success-Response:
 *     { 
 *        "total":"1",
 *        "data":[ 
 *           { 
 *              "id":22,
 *              "owner":{ 
 *                 "id":23,
 *                 "firstName":"Aruna",
 *                 "lastName":"Attanayake",
 *                 "profileImage":"",
 *                 "email":"aruna470@gmail.com",
 *                 "phone":"773959693",
 *                 "type":1,
 *                 "rating":0
 *              },
 *              "tenant":{ 
 *                 "id":24,
 *                 "firstName":"Esandu",
 *                 "lastName":"Attanayake",
 *                 "profileImage":"http:\/\/s3.amazonaws.com\/olarent-user-files1\/1914079.jpg?X-Amz-Content-Sha256=e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAJZSW2KDXKGTVRO5Q%2F20160210%2Fus-east-1%2Fs3%2Faws4_request&X-Amz-Date=20160210T050946Z&X-Amz-SignedHeaders=Host&X-Amz-Expires=18000&X-Amz-Signature=f13152e53139dd61ba72ae900bfae260ae7f515b4cd802ea6a383e21712eab37",
 *                 "email":"aruna@app-monkeyz.com",
 *                 "phone":"+9478956327",
 *                 "type":2,
 *                 "rating":4
 *              },
 *              "code":"892381",
 *              "name":"Lions Gate",
 *              "description":"Four story house",
 *              "address":"Colombo",
 *              "city":"Kadawatha",
 *              "cost":15,
 *              "status":2,
 *              "imageName":null,
 *              "imageUrl":"",
 *              "zipCode":"6254",
 *              "noOfRooms":2,
 *              "size":1000,
 *              "keyMoney":100,
 *              "currentRentDueAt":"2016-04-08",
 *              "totalPendingPayments":"45USD"
 *           }
 *        ]
 *     }
 *
 * @apiExample Example Request:
 * api/property/due-payment
 * api/property/due-payment?isOnBhf=1
 *
 */
 
/************ Create Property On Behalf of Owner *********/
 
/**
 * @api {post} http://<base-url>/api/property/on-behalf-of-create Create Property on Behalf of Owner
 * @apiDescription Tenant himself add property and owner bank details on behalf of owner and schedule the payments.
 * - Refer "Property Object" and "User Object" for parameter details. Following example illustrates the valid parameters for this request.
 * Rest of the parameters described in "Property Object" and "User Object" are used for viewing and some other requests.
 * - Mandatory fields from "PropertyObject" - payDay, duration, payKeyMoney, keyMoney, cost
 * - Mandatory fields from "UserObject" - firstName, lastName, email, bankName, bankAccountName, iban, swift
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_EXISTS
 * - access-token required in header.
 *
 * @apiName CreatePropertyOnBehalfOfOwner
 * @apiGroup Property
 *
 * @apiExample Example Request:
 *     { 
 *        "payDay":10,
 *        "duration":5,
 *        "payKeyMoney":1,
 *        "keyMoney":100,
 *        "cost":100,
 *        "user":{ 
 *           "firstName":"Yohan",
 *           "lastName":"Piyadigamage",
 *           "email":"yohanpiya@gmail.com",
 *           "bankName":"HNB",
 *           "bankAccountName":"Testname",
 *           "iban":"4569 89874",
 *           "swift":"12"
 *        }
 *     }
 */
 
/************ Update Property On Behalf of Owner *********/
 
/**
 * @api {put} http://<base-url>/api/property/on-behalf-of-update Update Property on Behalf of Owner
 * @apiDescription Tenant himself update property and owner bank details that he created. Allow to update only owner bank and profile details.
 * - Refer "Property Object" and "User Object" for parameter details. Following example illustrates the valid parameters for this request.
 * Rest of the parameters described in "Property Object" and "User Object" are used for viewing and some other requests.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_NOT_EXISTS
 * - access-token required in header.
 *
 * @apiName UpdatePropertyOnBehalfOfOwner
 * @apiGroup Property
 *
 * @apiExample Example Request:
 *    { 
 *       "user":{ 
 *          "firstName":"Yohan",
 *          "lastName":"Piyadigamage",
 *          "email":"yohanpiya@gmail.com",
 *          "bankName":"HNB",
 *          "bankAccountName":"Testname",
 *          "iban":"4569 89874",
 *          "swift":"12"
 *       }
 *    }
 *
 */
 
/************ Get On Behalf of Property List *********/
 
/**
 * @api {get} http://<base-url>/api/property/due-payment Get On Behlaf of Property List
 * @apiDescription Please refer "Get Due Payment List"
 * @apiName GetOnBeHalfOfPropertyList
 * @apiGroup Property
 */
 
/************ Get On Behalf of Property Details *********/
 
/**
 * @api {get} http://<base-url>/api/property/:id Get On Behlaf of Property Details
 * @apiDescription Please refer "Get Property"
 * @apiName GetOnBeHalfOfPropertyDetails
 * @apiGroup Property
 */
 
 
/************ Get Property Public *********/
 
/**
 * @api {get} http://<base-url>/api/property/public-view/:id Get Property Public
 * @apiDescription Retrieve property details for public view page. Here it will not return tenant details. 
 * Also do not check the property availability.
 * - Success response - "Property Object" 
 * - Failed response - "Common Response".Possible status codes are RECORD_NOT_EXISTS. 
 *
 * @apiName GetPropertyPublic
 * @apiGroup Property
 *
 * @apiParam {number} id Property id.
 *
 * @apiExample Example Response:
 *     { 
 *        "id":1,
 *        "description":"Testrr",
 *        "name":"Testdd",
 *        "address":"Colombo",
 *        "city":"Colombo",
 *        "cost":"15",
 *        "size":1000,
 *        "noOfRooms":2,
 *        "zipCode":"6254",
 *        "imageName":"45682.jpg",
 *        "thumbImageName":"thumb_45682.jpg",
 *        "owner": {
 *            "id": 6,
 *            "firstName": "Yohan",
 *            "lastName": "Hirimuthugoda",
 *            "email": "yohan@gmail.com",
 *            "type": 1,
 *            "phone": "773959693",
 *            "profileImage":"test.jpg"
 *        },
 *        "tenant": {},
 *        "images":[
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":0
 *           },
 *           { 
 *              "imageName":"thumb_stone.jpeg",
 *              "thumbImageName":"thumb_stone.jpeg",
 *              "isDefault":1
 *           }
 *        ]
 *     }
 */

/************ Get Share Meta Data *********/

/**
 * @api {get} http://<base-url>/api/property/get-share-meta-data/:id Get Share Meta Data
 * @apiDescription Retrieve meta tags for social sharing page.
 * - Success response - HTML content. Refer following example
 * - Failed response - empty.
 *
 * @apiName GetShareMetaData
 * @apiGroup Property
 *
 * @apiParam {number} id Property id.
 *
 * @apiExample Example Response:
 *    <!-- FB Share -->
 *    <meta property="og:description" content="Lille" />
 *    <meta property="og:title" content="6 Rooms House" />
 *    <meta property="og:image" content="https://s3.amazonaws.com/olarent-user-files1/prop_145_1455096708279.jpg" />
 *
 *    <!-- Twitter Share -->
 *    <meta name="twitter:card" content="summary_large_image">
 *    <meta name="twitter:title" content="6 Rooms House">
 *    <meta name="twitter:description" content="Lille">
 *    <meta name="twitter:image" content="https://s3.amazonaws.com/olarent-user-files1/prop_145_1455096708279.jpg"> *
 */