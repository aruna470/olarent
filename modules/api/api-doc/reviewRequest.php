<?php
/************ Create Review Request *********/
 
/**
 * @api {post} http://<base-url>/api/review-request Create Review Request
 * @apiDescription Add new review request
 * - Refer "Review Request Object" for parameter details. Following example illustrates the valid parameters for review request create.
 * Rest of the parameters described in "Review Request Object" are used for viewing and some other requests.
 * - Mandatory fields - receiverUserId.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, RECORD_EXISTS
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup ReviewRequest
 *
 * @apiExample Example Request:
 *    {
 *       "receiverUserId":10
 *    }
 *
 */
 
/************ Get Review Requests *********/
 
/**
 * @api {get} http://<base-url>/api/review-requests Get Review Request List
 * @apiDescription Get review request list
 * - Success response - "List Object" containing multiple instances of "Review Request Object".
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetReviewRequests
 * @apiGroup ReviewRequest
 *
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     {
 *        "total":"2",
 *        "data":[
 *           {
 *              "id":12,
 *              "requesterUserId":24,
 *              "receiverUserId":23,
 *              "createdAt":"2016-01-12 00:00:00",
 *              "requester":{
 *                 "id":24,
 *                 "firstName":"Esandu",
 *                 "lastName":"Attanayake",
 *                 "email":"aruna@app-monkeyz.com",
 *                 "type":2,
 *                 "phone":"773959694",
 *                 "profileImage":"test.jpg"
 *              },
 *              "receiver":{
 *                 "id":23,
 *                 "firstName":"Aruna",
 *                 "lastName":"Attanayake",
 *                 "email":"aruna470@gmail.com",
 *                 "type":1,
 *                 "phone":"773959693",
 *                 "profileImage":"test.jpg"
 *              }
 *           }
 *        ]
 *     }
 *
 *
 * @apiExample Example Request:
 * api/review-requests?page=1&limit=2
 *
 */