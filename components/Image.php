<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * Helper class to create thumbnail images
 */
class Image extends Component
{
    /**
     * Retrieve file mime type
     * @param string $sourcePath Source file path
     * @return string mime type
     */
    public function getMimeType($sourcePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $sourcePath);
        finfo_close($finfo);

        return $mimeType;
    }

    public function resizeByWidth($sourcePath, $destinationPath, $tgtWidth = 900, $quality = 70)
    {
        $status = false;
        $path = '';
        $info = getimagesize($sourcePath);
        if (!empty($info)) {
            $quality = $this->getQuality($info['mime'], $quality);
            $sourceImage = $this->createImageFromDiffMimeTypes($info['mime'], $sourcePath);
            if ("" != $sourceImage) {
                $sourceImage = $this->rotateImage($sourcePath, $sourceImage, $info['mime']);
                $width = imagesx($sourceImage);
                $height = imagesy($sourceImage);
                $tgtHeight = floor($height * ($tgtWidth / $width));
                $virtualImage = imagecreatetruecolor($tgtWidth, $tgtHeight);

                if($info['mime'] == "image/gif" or $info['mime'] == "image/png"){
                    imagecolortransparent($virtualImage, imagecolorallocatealpha($virtualImage, 0, 0, 0, 127));
                    imagealphablending($virtualImage, false);
                    imagesavealpha($virtualImage, true);
                }

                imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $tgtWidth, $tgtHeight, $width, $height);
                if ($this->createImage($info['mime'], $destinationPath, $virtualImage, $quality)) {
                    $status = true;
                }
            }
        }

        return $status;
    }

    /**
     * Compress
     * @param string $sourcePath Source file path
     * @param string $destinationPath Destination file path
     * @param integer $quality Image quality
     * @return string compressed file path
     */
    public function compressImage($sourcePath, $destinationPath, $quality = 90)
    {
        $info = getimagesize($sourcePath);
        $quality = $this->getQuality($info['mime'], $quality);
        $image = $this->createImageFromDiffMimeTypes($info['mime'], $sourcePath);
        $this->createImage($info['mime'], $destinationPath, $image, $quality);

        return $destinationPath;
    }

    /**
     * Create image for different file types like jpg,png etc.
     * @param string $mimeType Mime type
     * @param string $imagePath Image path
     * @return mixed $img Created image
     */
    private function createImageFromDiffMimeTypes($mimeType, $imagePath)
    {
        $img = "";

        switch ($mimeType) {
            case 'image/jpeg':
                $img = imagecreatefromjpeg($imagePath);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($imagePath);
                break;
            case 'image/png':
                $img = imagecreatefrompng($imagePath);
                break;
        }

        return $img;
    }

    /**
     * Create image for different file types like jpg,png etc.
     * @param string $mimeType Mime type
     * @param string $filePath File path
     * @param mixed $image Created virtual image
     * @param integer $quality Image quality
     * @return mixed $img Created image
     */
    private function createImage($mimeType, $filePath, $image, $quality)
    {
        $status = false;

        switch ($mimeType) {
            case 'image/jpeg':
                $status = imagejpeg($image, $filePath, $quality);
                break;
            case 'image/gif':
                $status = imagegif($image, $filePath);
                break;
            case 'image/png':
                $status = imagepng($image, $filePath, $quality);
                break;
        }

        return $status;
    }

    /**
     * Calculate quality by mime type
     * @param string $mimeType Image mime type
     * @param integer $quality Quality value between 0 - 100
     * @return integer Quality
     */
    private function getQuality($mimeType, $quality)
    {
        $finalQuality = $quality;
        if ($mimeType == 'image/png') {
            $finalQuality = round($quality/10);
        }

        return $finalQuality;
    }

    /**
     * If image is rotated, rotate back to original state
     * @param string $sourcePath Source file path
     * @param mixed $image Image resource
     * @param string $mimeType Image mime type
     * @return rotated image resource
     */
    public function rotateImage($sourcePath, $image, $mimeType)
    {
        if ($mimeType == 'image/jpeg') {
            $exif = exif_read_data($sourcePath);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }

                    if ($deg) {
                        $image = imagerotate($image, $deg, 0);
                    }
                }
            }
        }

        return $image;
    }

    /**
     * Find image extension based on mime type
     * @param string $sourcePath Source file path
     * @return string image extension
     */
    function getImageExt($sourcePath)
    {
        $info = getimagesize($sourcePath);
        $ext = '';
        switch ($info['mime']) {
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/gif':
                $ext = 'gif';
                break;
            case 'image/png':
                $ext = 'png';
                break;
        }

        return $ext;
    }

}