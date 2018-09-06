<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * AppLogger class. Which writes user activities
 */
class AppLogger extends Component
{
    const CRITICAL = 0;
    const WARNING = 1;
    const INFO = 2;
    const DEBUG = 3;

    /**
     * Log level labels for print in log
     */
    private $logLevelLabel = array(
        self::CRITICAL => 'CRITICAL',
        self::WARNING => 'WARNING',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG'
    );

    /**
     * Log path
     */
    private $logPath = '/tmp/';

    /**
     * Log Server socket
     */
    private $logSocket = 'localhost:0';

    /**
     * Log file name
     */
    private $logName = '';

    /**
     * Logged user name
     */
    public $username = 'Guest';

    /**
     * Log type 1 - Activity log
     */
    public $logType = 1;

    /**
     * Log data
     */
    public $logParams = array();

    /**
     * Current log level
     */
    public $logLevel = self::DEBUG;

    /**
     * Whether log request come from console application
     */
    public $isConsole = false;

    /**
     * Application identifier.Since we write all clients logs to same log file
     */
    public $appId = 'N/A';

    /**
     * User action
     */
    public $action = 'N/A';

    /**
     * Uniqid for transaction
     */
    public $uniqid = 'N/A';

    function init()
    {

    }

    /**
     * Set log data to an array
     */
    private function setLogParams()
    {
        $logParams = $this->logParams[$this->logType];
        $this->logPath = $logParams['logPath'];
        $this->logName = date('Ymd') . $logParams['logName'];
        $this->logSocket = $logParams['logSocket'];
        $this->logLevel = $logParams['logLevel'];
        $this->isConsole = $logParams['isConsole'];
    }

    /**
     * Prepare log message and write accordingly
     *
     * @param string	$message Log message
     * @param integer $logLevel Log level
     */
    function writeLog($message, $params = [], $logLevel = self::INFO)
    {
        // Initialize log params according log type
        $this->setLogParams();

//        if (!$this->isConsole && is_object(Yii::$app->user->identity)) {
//            $this->username = Yii::$app->user->identity->email;
//        }

        $msg = '';

        switch ($this->logType) {

            // User activity log.
            // Format: Date|Time|Username|IP|Domain|Log Level|Activity
            case 1:

                $date = date('Y-m-d|H:i:s');
                $ip = $_SERVER['REMOTE_ADDR'];
                $domain = $_SERVER['HTTP_HOST'];
                $data = json_encode($params);

                $msg = "{$date}|{$this->username}|{$ip}|{$domain}|{$this->logLevelLabel[$logLevel]}|{$this->action}|{$message}|{$data} \n";

                break;

            // API log.
            // Format: Date|Time|Uniqid|Username|IP|Domain|Log Level|Activity
            case 2:

                $date = date('Y-m-d|H:i:s');
                $ip = $_SERVER['REMOTE_ADDR'];
                $domain = $_SERVER['HTTP_HOST'];
                $data = json_encode($params);

                $msg = "{$date}|{$this->uniqid}|{$this->username}|{$ip}|{$domain}|{$this->logLevelLabel[$logLevel]}|{$this->action}|{$message}|{$data} \n";

                break;

            // Daemon activity log.
            // Format: Date|Time|UniqueId|Daemon Name|Log Level|Activity|Additional params
            case 3:

                $date = date('Y-m-d|H:i:s');
                $data = json_encode($params);
                $msg = "{$date}|{$this->uniqid}|{$this->action}|{$this->logLevelLabel[$logLevel]}|{$message}|{$data} \n";

                break;
        }

        $logFile = $this->logPath . $this->logName;

        /*
        if (!is_file($logFile)) {
            $handle = fopen($logFile, "w+");
            fclose($handle);
            chmod($logFile, 777);
            @exec("chmod 777 {$logFile}");
        }
        */

        if ($logLevel <= $this->logLevel) {
            file_put_contents($logFile, $msg, FILE_APPEND);
        }
    }
}

?>