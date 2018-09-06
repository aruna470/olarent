<?php
/************ S3 File Upload *********/
 
/**
 * @api {post} http://<base-url>/api/util/s3upload S3 File Upload
 * @apiDescription Add new user review
 * - Refer "S3 File Object" for parameter details. Following example illustrates the valid parameters for file upload.
 * - Mandatory fields - file or fileData, fileName
 * - Success response - "Common Response" along with "extraParams" attribute which contains "fileName" & "url".
 * Possible response codes are SUCCESS, FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED
 * - Content type - multipart/form-data
 *
 * @apiName S3Upload
 * @apiGroup S3FileManagement
 *
 * @apiExample Example Request:
 *     file=<file object>&fileName=test.jpg&options={"compress":1, "thumbnail":1, "thumbnailWidth":40}
 *     or
 *     fileData=<base64 encoded string>&fileName=test.jpg&options={"compress":1, "thumbnail":1, "thumbnailWidth":40}
 *
 * @apiExample Example Response:
 *     { 
 *        "code":"SUCCESS",
 *        "message":null,
 *        "extraParams":{ 
 *           "fileName":"hello.jpeg",
 *           "url":"http://localhost",
 *           "thumbnailName":"thumb_hello.jpeg",
 *           "thumbnailUrl":"http://localhost"
 *        }
 *     }
 */
 
/************ Get S3 File URL *********/
 
/**
 * @api {get} http://<base-url>/api/util/s3file Get S3 File
 * @apiDescription Retrieve S3 URL of a file. 
 * - Success response - "Property Request Object" 
 * - Failed response - "Common Response".Possible status codes are FAILED, MISSING_MANDATORY_FIELD, VALIDATION_FAILED. 
 *
 * @apiName GetS3File
 * @apiGroup S3FileManagement
 *
 * @apiParam {String} fileName File name.
 * @apiParam {Number} signed Whether signed URL or not.1 – Signed, 0 - Unsigned. 
 * If you need unsigned(public) URL then file status need to be set public when uploading
 *
 * @apiExample Example Response:
 *     {
 *        "fileName":"hello.jpeg",
 *        "url":"http:\/\/s3-us-west-2.amazonaws.com\/in-bucket\/hello.jpeg?X-Amz-Content-Sha256=e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAJXKDVTOY22D5SFBQ%2F20160115%2Fus-west-2%2Fs3%2Faws4_request&X-Amz-Date=20160115T054637Z&X-Amz-SignedHeaders=Host&X-Amz-Expires=900&X-Amz-Signature=4a3fe9e0ebf9fd18644aeb9480add432095e90bc427c17d8481eff601f0db0a1"
 *     }
 *
 * @apiExample Example Request:
 * api/util/s3file?fileName=hello.jpeg&signed=1
 */