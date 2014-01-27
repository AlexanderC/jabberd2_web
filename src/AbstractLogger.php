<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/27/14
 * @time 10:41 AM
 */

namespace Jabberd2;


use Psr\Log\AbstractLogger as BaseLogger;

abstract class AbstractLogger extends BaseLogger
{
    /**
     * @param $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context)
    {
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }
} 