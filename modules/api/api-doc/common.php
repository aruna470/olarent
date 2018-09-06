<?php

/**
 * @api {auth-header} {} API Authentication
 * @apiDescription Need to send following parameters on request header. access-token is optional unless it is specifically mentioned.
 * @apiName APIAuth
 * @apiGroup General
 *
 * @apiHeader {String} api-key API access key.
 * @apiHeader {String} api-secret API access secret.
 * @apiHeader {String} [access-token] User access token obtained while user authentication. This need to be present when authenticated user trying to access content.
 * @apiHeader {String} content-type application/json
 */

/**
 * @api {common-response} {} Common Response
 * @apiDescription Following is the general response returned by the API.
 * @apiName Common Response
 * @apiGroup General
 *
 * @apiSuccess (Success) {String} code Status code.
 * @apiSuccess (Success) {String} message Status message.
 * @apiSuccess (Success) {Object} [extraParams] Contains additional attributes to be sent. Not always present.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "code": "SUCCESS",
 *       "message": ""
 *     }
 */
 
 /**
 * @api {auth-response} {} Authentication Response
 * @apiDescription This response returns only for user authentication.
 * @apiName Authentication Response
 * @apiGroup General
 *
 * @apiSuccess (Success) {Object} status Refer "Common Response".
 * @apiSuccess (Success) {String} token User access token. For each login new token will be generated.
 * @apiSuccess (Success) {Object} user Login user details. Refer "User Object" details.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *        "status": {
 *           "code": "SUCCESS",
 *           "message": null
 *        },
 *        "token": "565c1517e6047",
 *        "user": {
 *            "firstName": "Yohan",
 *            "lastName": "Piyadigamage",
 *            "email": "yohan@gmail.com",
 *            "type": 1,
 *               .
 *               .
 *               .
 *        }
 *     }
 */
