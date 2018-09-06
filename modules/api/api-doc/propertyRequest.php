<?php
/************ Create Property Request *********/
 
/**
 * @api {post} http://<base-url>/api/property-request Create Property Request
 * @apiDescription Add new property request
 * - Refer "Property Request Object" for parameter details. Following example illustrates the valid parameters for property request create.
 * Rest of the parameters described in "Property Request Object" are used for viewing and some other requests.
 * - Mandatory fields are code, payday, bookingDuration, payKeyMoneyCc.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup PropertyRequest
 *
 * @apiExample Example Request:
 *    {
 *       "code":"E636B6",
 *       "payDay":10,
 *       "bookingDuration":2,
 *       "payKeyMoneyCc":1
 *    }
 *
 */
 
/************ Get Property Request List *********/
 
/**
 * @api {get} http://<base-url>/api/property-requests Get Property Request List
 * @apiDescription Search property requests by various parameters. If you call this method from Tenant login then
 * returns requests made by partcular Tenant. If you call this method form Owner login then returns requests received
 * by particular owner.
 * - Success response - "List Object" containing multiple instances of "Property Request Object".
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetPropertyRequests
 * @apiGroup PropertyRequest
 *
 * @apiParam {String} [code] Property code.
 * @apiParam {Number} [propertyId] Property id.
 * @apiParam {Number} [status] Property request status. Refer "Property Request Object" for possible statuses..
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     {
 *         "count": "1",
 *         "data": [
 *             {
 *                 "id": 6,
 *                 "code": "E636B6",
 *                 "tenantUserId": 7,
 *                 "payDay": 10,
 *                 "bookingDuration": 2,
 *                 "status": 0,
 *                 "owner": {
 *                     "id": 6,
 *                     "firstName": "Yohan",
 *                     "lastName": "Hirimuthugoda",
 *                     "email": "aruna4.70@gmail.com",
 *                     "type": 1,
 *                     "phone": "773959693",
 *                     "profileImage":"test.jpg"
 *                 },
 *                 "tenant": {
 *                     "id": 7,
 *                     "firstName": "Yoooohan",
 *                     "lastName": "Girimuthugoda",
 *                     "email": "ar.una470@gmail.com",
 *                     "type": 2,
 *                     "phone": "773959694",
 *                     "profileImage":"test.jpg"            
 *                }
 *             }
 *         ]
 *     }
 *
 * @apiExample Example Request:
 * api/property-requests?page=1&limit=5
 *
 */
 
/************ Get Property Request *********/
 
/**
 * @api {get} http://<base-url>/api/property-request/:id Get Property Request
 * @apiDescription Retrieve existing property request details. 
 * - Success response - "Property Request Object" 
 * - Failed response - "Common Response".Possible status codes are RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName GetPropertyRequest
 * @apiGroup PropertyRequest
 *
 * @apiParam {number} id Property request id.
 *
 * @apiExample Example Response:
 *     {
 *         "id": 6,
 *         "code": "E636B6",
 *         "tenantUserId": 7,
 *         "payDay": 10,
 *         "bookingDuration": 2,
 *         "status": 0,
 *         "owner": {
 *             "id": 6,
 *             "firstName": "Yohan",
 *             "lastName": "Hirimuthugoda",
 *             "email": "aruna4.70@gmail.com",
 *             "type": 1,
 *             "phone": "773959693",
 *             "profileImage":"test.jpg"
 *         },
 *         "tenant": {
 *             "id": 7,
 *             "firstName": "Yoooohan",
 *             "lastName": "Girimuthugoda",
 *             "email": "ar.una470@gmail.com",
 *             "type": 2,
 *             "profileImage":"test.jpg"
 *             "phone": "773959694",
 *         }
 *     }
 *
 * @apiExample Example Request:
 * api/property-request/6
 */
 
/************ Accept Property Request *********/
 
/**
 * @api {put} http://<base-url>/api/property-request/accept/:id Accept Property Request
 * @apiDescription Owner accept property request sent by a Tenant. 
 * - Success response - "Common Response".Possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName AcceptPropertyRequest
 * @apiGroup PropertyRequest
 *
 * @apiParam {number} id Property request id.
 *
 * @apiExample Example Request:
 * api/property-request/accept/6
 */
 
/************ Reject Property Request *********/
 
/**
 * @api {put} http://<base-url>/api/property-request/reject/:id Reject Property Request
 * @apiDescription Owner reject property request sent by a Tenant. 
 * - Success response - "Common Response".Possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName RejectPropertyRequest
 * @apiGroup PropertyRequest
 *
 * @apiParam {number} id Property request id.
 *
 * @apiExample Example Request:
 * api/property-request/reject/6
 */
 
/************ Delete Property Request *********/
 
/**
 * @api {delete} http://<base-url>/api/property-request/:id Delete Property Request
 * @apiDescription Tenant can delete property requests on pending status.
 * - Success response - "Common Response".Possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName DeletePropertyRequest
 * @apiGroup PropertyRequest
 *
 * @apiParam {number} id Property request id.
 *
 * @apiExample Example Request:
 * api/property-request/6
 */