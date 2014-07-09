<?php

/**
 * Logger
 *
 * This is the primary logger for a Freya application. You may provide
 * a Log Writer in conjunction with this Log to write to various output
 * destinations (e.g. a file). This class provides this interface:
 *
 * debug(mixed $object, array $context)
 * info(mixed $object, array $context)
 * notice(mixed $object, array $context)
 * warning(mixed $object, array $context)
 * error(mixed $object, array $context)
 * critical(mixed $object, array $context)
 * alert(mixed $object, array $context)
 * emergency(mixed $object, array $context)
 * log(mixed $level, mixed $object, array $context)
 *
 * This class assumes only that your Log Writer has a public `write()` method
 * that accepts any object as its one and only argument. The Log Writer
 * class may write or send its argument anywhere: a file, STDERR,
 * a remote web API, etc. The possibilities are endless.
 */

namespace Freya\Logger;

class Logger extends \Psr\Log\AbstractLogger
{
    private $logLevel;
    private $logLevels = array(
        \Psr\Log\LogLevel::EMERGENCY => 0,
        \Psr\Log\LogLevel::ALERT     => 1,
        \Psr\Log\LogLevel::CRITICAL  => 2,
        \Psr\Log\LogLevel::ERROR     => 3,
        \Psr\Log\LogLevel::WARNING   => 4,
        \Psr\Log\LogLevel::NOTICE    => 5,
        \Psr\Log\LogLevel::INFO      => 6,
        \Psr\Log\LogLevel::DEBUG     => 7
    );

    private $fileHandle         = null;
    private $dateFormat         = 'Y-m-d G:i:s.u';
    private $defaultPermissions = 0777;

    /**
     * Class constructor
     *
     * @param string  $logDirectory       File path to the logging directory
     * @param integer $logLevelThreshold  The LogLevel Threshold
     * @return void
     */
    public function __construct($logDirectory, $logLevel = \Psr\Log\LogLevel::DEBUG)
    {
        $this->logLevel = $logLevel;

        $logDirectory = rtrim($logDirectory, '\\/');
        if (! file_exists($logDirectory)) {
            if (! mkdir($logDirectory, $this->defaultPermissions, true)) {
                throw new \RuntimeException('Unable to create our log directory. Check permissions.');
            }
        }

        $this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . 'log_' . date('Y-m-d') . '.txt';
        if (file_exists($this->logFilePath) && !is_writable($this->logFilePath)) {
            throw new \RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
        }
        
        $this->fileHandle = fopen($this->logFilePath, 'a');
        if ( ! $this->fileHandle) {
            throw new \RuntimeException('The file could not be opened. Check permissions.');
        }
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * Sets the date format used by all instances of KLogger
     * 
     * @param string $dateFormat Valid format string for date()
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Sets the Log Level Threshold
     * 
     * @param string $dateFormat Valid format string for date()
     */
    public function setLogLevelThreshold($logLevelThreshold)
    {
        $this->logLevelThreshold = $logLevelThreshold;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset($this->logLevels[$level])) {
            throw new \Psr\Log\InvalidArgumentException('Invalid log level supplied to function');
        } elseif ($this->logLevels[$level] <= $this->logLevels[$this->logLevel]) {
            $message = $this->formatMessage($level, $message, $context);
            $this->write($message);
        }
    }

    /**
     * Writes a line to the log without prepending a status or timestamp
     *
     * @param string $line Line to write to the log
     * @return void
     */
    public function write($message)
    {
        if (! is_null($this->fileHandle)) {
            if (fwrite($this->fileHandle, $message) === false) {
                throw new \RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
            }
        }
    }

    /**
     * Formats the message for logging.
     *
     * @param  string $level   The Log Level of the message
     * @param  string $message The message to log
     * @param  array  $context The context
     * @return string
     */
    private function formatMessage($level, $message, $context)
    {
        $level = strtoupper($level);
        return "[{$this->getTimestamp()}] [{$level}] {$this->interpolate($message)}".PHP_EOL;
    }

    /**
     * Gets the correctly formatted Date/Time for the log entry.
     * 
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work correctly, so here it is.
     * 
     * @return string
     */
    private function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $originalTime));

        return $date->format($this->dateFormat);
    }
    
    /**
    * Interpolates context values into the message placeholders.
    *
    * @param  string $message    The message to be interpolated
    * @param  array  $context    The key => value pairs to be used for replacement
    * @return string
    */
    function interpolate($message, array $context = array())
    {
        $replace = array();
        
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        
        return strtr($message, $replace);
    }
}
