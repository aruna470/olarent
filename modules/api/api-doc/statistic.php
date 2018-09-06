<?php
/************ Owner Dashboard *********/
 
/**
 * @api {get} http://<base-url>/api/statistic/owner-dashboard Owner Dashboard
 * @apiDescription Retrieve statistical details of owner
 * - Success response - "Statistic Object".
 * - access-token is required in header.
 *
 * @apiName OwnerDashboard
 * @apiGroup Statistic
 *
 * @apiSuccessExample Success-Response:
 *     { 
 *        "incomeSummary":{ 
 *           "received":"150",
 *           "pending":15
 *        },
 *        "incomeHistory":[ 
 *           { 
 *              "month":"JAN",
 *              "income":"150"
 *           },
 *           { 
 *              "month":"DEC",
 *              "income":0
 *           },
 *           { 
 *              "month":"NOV",
 *              "income":0
 *           },
 *           { 
 *              "month":"OCT",
 *              "income":0
 *           },
 *           { 
 *              "month":"SEP",
 *              "income":0
 *           },
 *           { 
 *              "month":"AUG",
 *              "income":0
 *           },
 *           { 
 *              "month":"JUL",
 *              "income":0
 *           },
 *           { 
 *              "month":"JUN",
 *              "income":0
 *           },
 *           { 
 *              "month":"MAY",
 *              "income":0
 *           },
 *           { 
 *              "month":"APR",
 *              "income":0
 *           },
 *           { 
 *              "month":"MAR",
 *              "income":0
 *           },
 *           { 
 *              "month":"FEB",
 *              "income":0
 *           }
 *        ]
 *     }
 *
 */