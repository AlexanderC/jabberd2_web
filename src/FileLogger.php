<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/27/14
 * @time 10:26 AM
 */

namespace Jabberd2;


use Psr\Log\LogLevel;

class FileLogger extends AbstractLogger
{
    const TPL = "[%s] jabberd2_web.%s: %s\n";

    /**
     * @var string
     */
    protected $logDir;

    /**
     * @param string $logDir
     * @throws \RuntimeException
     */
    public function __construct($logDir)
    {
        $this->logDir = realpath($logDir);

        if(!is_dir($this->logDir) || !is_writeable($this->logDir)) {
            throw new \RuntimeException("Log directory should exists and be writeable");
        }
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return $this->logDir;
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
        $text = sprintf(self::TPL, date("d.m.Y h:i:s"), $level, $this->interpolate($message, $context));

        $file = $this->getRotateLogFile();

        file_put_contents($file, $text, LOCK_EX | FILE_APPEND);
    }

    /**
     * @return string
     */
    protected function getRotateLogFile()
    {
        return sprintf("%s/%s.log", $this->logDir, date("d-m-Y"));
    }
} 