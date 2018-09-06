<?php
/************ Create User Review *********/
 
/**
 * @api {post} http://<base-url>/api/user-review Create User Review
 * @apiDescription Add new user review
 * - Refer "User Review Object" for parameter details. Following example illustrates the valid parameters for user review create.
 * Rest of the parameters described in "User Review Object" are used for viewing and some other requests.
 * - Mandatory fields - userId, title, rating, reviewRequestId.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, DUPLICATE_REVIEW
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup User Review
 *
 * @apiExample Example Request:
 *     {
 *         "userId":24,
 *         "rating":4,
 *         "title":"test",
 *         "comment":"test comment",
 *         "reviewRequestId":1
 *     }
 *
 */
 
/************ Get User Review List *********/
 
/**
 * @api {get} http://<base-url>/api/user-reviews Get User Reviews
 * @apiDescription Get user review list
 * - Success response - "List Object" containing multiple instances of "User Review Object".
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetUserReviews
 * @apiGroup User Review
 *
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     { 
 *        "total":"1",
 *        "data":[ 
 *           { 
 *              "id":6,
 *              "userId":24,
 *              "rating":2,
 *              "title":"test",
 *              "comment":"test comment",
 *              "createdAt":"2016-01-13 08:10:45",
 *              "reviewedUser":{ 
 *                 "id":23,
 *                 "firstName":"Aruna",
 *                 "lastName":"Attanayake",
 *                 "profileImage":null,
 *                 "email":"aruna470@gmail.com",
 *                 "phone":"773959693",
 *                 "type":1
 *              }
 *           }
 *        ]
 *     }
 *
 * @apiExample Example Request:
 * api/user-reviews?page=1&limit=1
 *
 */