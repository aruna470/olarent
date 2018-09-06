<?php

/************ Create Payment Plan *********/
 
/**
 * @api {post} http://<base-url>/api/review-request Create Payment Plan
 * @apiDescription Add payment plan. User can have only one payment plan.
 * - Refer "Payment Plan Object" for parameter details. Following example illustrates the valid parameters for payment plan create.
 * Rest of the parameters described in "Payment Plan Object" are used for viewing and some other requests.
 * - Mandatory fields depend on the gateway. 
 * If Adyen - paymentGateway, cardType, adyenPspReference, adyenShopperReference. 
 * If Stripe - paymentGateway, stripeToken.
 * - Success response - "Common Response", Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED, PLAN_EXISTS
 * - access-token required in header.
 *
 * @apiName Create
 * @apiGroup PaymentPlan
 *
 * @apiExample Example Request - Adyen:
 *    {
 *        "paymentGateway":1,
 *        "cardType":"visa",
 *        "adyenPspReference":"1234",
 *        "adyenShopperReference":"1234"
 *    }
 *
 * @apiExample Example Request - Stripe:
 *    { 
 *       "paymentGateway":2,
 *       "stripeToken":"tok_17s5mEHr7NyAKbjcS5IVzxir",
 *       "cardHolderName":"Dhammika"
 *    }
 */
 
/************ Get Payment Plan *********/
 
/**
 * @api {get} http://<base-url>/api/payment-plan/:id Get Payment Plan
 * @apiDescription Retrieve payment plan details of particular user. 
 * You can use this method to check whether user has configured a payment plan before making property request. 
 * - Success response - "Property Plan Object" 
 * - Failed response - "Common Response". Possible status codes are RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName GetPaymentPlan
 * @apiGroup PaymentPlan
 *
 * @apiParam {number} id Payment plan id.
 *
 * @apiExample Example Response:
 *     { 
 *        "id":4,
 *        "expire":"2018-08",
 *        "cardType":"visa",
 *        "cardNumber":"1111",
 *        "adyenPspReference":"1234",
 *        "adyenShopperReference":"1234",
 *        "paymentGateway":1,
 *        "cardHolderName":"Dhammika"
 *     }
 */
 
/************ Delete Payment Plan *********/
 
/**
 * @api {delete} http://<base-url>/api/payment-plan/:id Delete Payment Plan
 * @apiDescription Delete a payment plan.
 * - Success response - "Common Response". Possible status codes are SUCCESS, FAILED, RECORD_NOT_EXISTS. 
 * - access-token is required in header.
 *
 * @apiName DeletePaymentPlan
 * @apiGroup PaymentPlan
 *
 * @apiParam {number} id Payment plan id.
 *
 * @apiExample Example Request:
 * api/payment-plan/6
 */