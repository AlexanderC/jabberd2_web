<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/27/14
 * @time 10:26 AM
 */

namespace Jabberd2;


use Psr\Log\LogLevel;

class SyslogLogger extends AbstractLogger
{
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
        syslog(
            $this->castLevelPsrToSystem($level),
            $this->interpolate($message, $context)
        );
    }

    protected function castLevelPsrToSystem($level)
    {
        $sysLevel = null;

        switch($level)
        {
            case LogLevel::EMERGENCY:
                $sysLevel = LOG_EMERG;
                break;
            case LogLevel::ALERT:
                $sysLevel = LOG_ALERT;
                break;
            case LogLevel::CRITICAL:
                $sysLevel = LOG_CRIT;
                break;
            case LogLevel::DEBUG:
                $sysLevel = LOG_DEBUG;
                break;
            case LogLevel::ERROR:
                $sysLevel = LOG_ERR;
                break;
            case LogLevel::INFO:
                $sysLevel = LOG_INFO;
                break;
            case LogLevel::NOTICE:
                $sysLevel = LOG_NOTICE;
                break;
            case LogLevel::WARNING:
                $sysLevel = LOG_WARNING;
                break;
            default: $sysLevel = LOG_NOTICE;
        }

        return $sysLevel;
    }
} 