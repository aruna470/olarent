<?php

namespace app\components;

use Yii;
use yii\base\Component;
use \DateTime;
use \DateTimeZone;

class Util extends Component
{

    /**
     * Retrieve UTC date time
     * @param string $format Date Time format
     * @return string UTC date time
     */
    public function getUtcDateTime($dateTime = null, $sourceTz = null, $format = 'Y-m-d H:i:s')
    {
        if (null != $dateTime) {
            $date = new DateTime($dateTime, new DateTimeZone($sourceTz));
            $date->setTimezone(new DateTimeZone('UTC'));
            return $date->format($format);
        } else {
            return gmdate('Y-m-d H:i:s');
        }
    }

    /**
     * Retrieve UTC date
     * @param string $format Date Time format
     * @return string UTC date
     */
    public function getUtcDate($dateTime = null, $sourceTz = null, $format = 'Y-m-d')
    {
        if (null != $dateTime) {
            $date = new DateTime($dateTime, new DateTimeZone($sourceTz));
            $date->setTimezone(new DateTimeZone('UTC'));
            return $date->format($format);
        } else {
            return gmdate('Y-m-d');
        }

        return gmdate($format);
    }

    /**
     * Convert specific date time to another date time based on Timezone
     * @param string $dateTime Stored datetime
     * @param string $destinationTz Date time will be converted to this timezone
     * @param string $sourceTz Currently date time stored timezone
     * @param string $format Date Time format
     * @return string converted date time
     */
    public function getLocalDateTime($dateTime, $destinationTz, $sourceTz = 'UTC', $format = 'Y-m-d H:i:s')
    {
        if ('' != $dateTime) {
            $date = new DateTime($dateTime, new DateTimeZone($sourceTz));
            $date->setTimezone(new DateTimeZone($destinationTz));
            return $date->format($format);
        }

        return '';
    }

    /**
     * Convert specific date time to another date time based on Timezone
     * @param string $dateTime Stored datetime
     * @param string $destinationTz Date time will be converted to this timezone
     * @param string $sourceTz Currently date time stored timezone
     * @param string $format Date Time format
     * @return string converted date
     */
    public function getLocalDate($dateTime, $destinationTz, $sourceTz = 'UTC', $format = 'Y-m-d')
    {
        if ('' != $dateTime) {
            $date = new DateTime($dateTime, new DateTimeZone($sourceTz));
            $date->setTimezone(new DateTimeZone($destinationTz));
            return $date->format($format);
        }

        return '';
    }

    /**
     * Convert specific date time to another date time based on Timezone
     * @param string $dateTime Stored datetime
     * @param string $destinationTz Date time will be converted to this timezone
     * @param string $sourceTz Currently date time stored timezone
     * @param string $format Date Time format
     * @return string converted date
     */
    public function getTzSpecificDateTime($dateTime, $destinationTz, $sourceTz = 'UTC', $format = 'Y-m-d H:i:s')
    {
        $date = new DateTime($dateTime, new DateTimeZone($sourceTz));
        $date->setTimezone(new DateTimeZone($destinationTz));
        return $date->format($format);
    }

    /**
     * Returns available timezone list
     */
    public function getTimeZoneList()
    {
        $tz = timezone_identifiers_list();

        return array_combine($tz, $tz);
    }

    /**
     * Prepare bootstrap lable segments to be diaplayed
     * @param string $type Label type
     * @param string $text Lable text
     * @return string bootstrap label
     */
    public function getBootLabel($type, $text)
    {
        return "<span class=\"label label-{$type}\">{$text}</span>";
    }

    /**
     * Handle popup window close.
     * @param string $parentRedirectUrl Parent page redirect URL on popup close
     * @param boolean $parentRefresh Whether to refresh parent page on popup close
     */
    public function closePopupWindow($parentRedirectUrl = null, $parentRefresh = false)
    {
        if ($parentRefresh) {
            $script = 'window.opener.location.reload(); window.close();';
        } else if (null == $parentRedirectUrl && !$parentRefresh) {
            $script = 'window.close();';
        } else {
            $script = "window.opener.location.href = '{$parentRedirectUrl}'; window.close();";
        }
        echo "<script>{$script}</script>";
    }

    /**
     * Register grid item delete script with confirmation. This is common to all grids
     * @param View $view View instance
     */
    public function registerGridDelScript($view)
    {
        $confirmMsg = Yii::t('app', 'Are you sure you want to delete this item?');
        $view->registerJs("
			$(document.body).on('click', '#delete' ,function() {
				var url = $(this).attr('href');
				bootbox.confirm('{$confirmMsg}', function(result) {
					if (result) {
						location.href = url;
					}
				});
				return false;
			});
		", $view::POS_END, 'delConfirm');
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or the trimmed result is an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value The value to be checked
     * @param boolean $trim Whether to perform trimming before checking if the string is empty. Defaults to true.
     * @return boolean Whether the value is empty
     */
    public function isEmpty($value, $trim = true)
    {
        return $value === null || $value === array() || $value === '' || $trim && is_scalar($value) && trim($value) === '';
    }

    /**
     * Convert URLs in a string to Links
     * @param string $text Text with urls
     * @param string $id Id to be appended to link
     * @param string $title Title of the link
     * @return string Text with links
     */
    public function convertTextUrlsToLinks($text, $id='', $title='')
    {
        $textWithLinks = $text;
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        preg_match_all($reg_exUrl, $text, $matches);
        $urls = @$matches[0];

        if ('' != $urls) {
            foreach ($urls as $url) {
                $href = "<a id='{$id}' title='{$title}' class='tool' target='_blank' href='{$url}'>{$url}</a>";
                $textWithLinks = str_replace($url, $href, $textWithLinks);
            }
        }

        return $textWithLinks;
    }

    public static function isArrayEmpty($array)
    {
        $result = true;

        if (is_array($array) && count($array) > 0) {
            foreach ($array as $value) {
                $result = $result && self::isArrayEmpty($value);
            }
        } else {
            $result = empty($array);
        }

        return $result;
    }
}
