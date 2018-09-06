<?php
/************ Get Notification Request *********/
 
/**
 * @api {get} http://<base-url>/api/notifications Get Notifications
 * @apiDescription Get user notifications
 * - Success response - "List Object" containing multiple instances of "Notification Object".
 * - Failed response - "Common Response", possible status codes are MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - access-token is required in header.
 *
 * @apiName GetNotifications
 * @apiGroup Notification
 *
 * @apiParam {Number} [viewStatus] Refer "Notification Object" for possible statuses.
 * @apiParam {Number} [limit] Number of records to return. Default 10
 * @apiParam {Number} [page] Page number. Start form 1
 *
 * @apiSuccessExample Success-Response:
 *     {
 *         "total": "2",
 *         "data": [
 *             {
 *                 "id": 2,
 *                 "message": "You have received a property request from Esandu",
 *                 "viewStatus": 0,
 *                 "createdAt": "2016-01-05 00:00:00"
 *             },
 *             {
 *                 "id": 1,
 *                 "message": "You have received a property request from aruna",
 *                 "viewStatus": 0,
 *                 "createdAt": "2016-01-01 00:00:00"
 *             }
 *         ]
 *     }
 *
 * @apiExample Example Request:
 * api/notifications?viewStatus=0&page=1&limit=2
 *
 */
 
 /************ Update Notification *********/
 
/**
 * @api {put} http://<base-url>/api/notification/:id Update Notification
 * @apiDescription Update view status of the notification. 
 * - Refer "Notification Object" for parameter details. Following example illustrates the valid parameters for notification update.
 * Rest of the parameters described in "Notification Object" are used for viewing and some other requests.
 * - Success response - "Common Response", possible response codes are SUCCESS, FAILED, VALIDATION_FAILED, RECORD_NOT_EXISTS
 * - access-token is required in header.
 *
 * @apiName Update
 * @apiGroup Notification
 *
 * @apiExample Example Request:
 *     {
 *        "viewStatus":1
 *     }
 */