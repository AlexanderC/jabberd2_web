<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 2:10 PM
 */

namespace Jabberd2;

class Config
{
    /**
     * @var bool
     */
    protected static $locked = false;

    /**
     * @var array
     */
    protected static $data = array();

    /**
     * @param array $data
     * @param bool $lock
     * @throws \RuntimeException
     */
    public static function setData(array $data, $lock = true)
    {
        if(self::isLocked()) {
            throw new \RuntimeException("Configuration is locked");
        }

        self::$data = self::arrayToObject($data);

        if($lock) {
            self::$locked = true;
        }
    }

    /**
     * @return bool
     */
    public static function isLocked()
    {
        return self::$locked;
    }

    /**
     * @param string $key
     */
    public static function get($key)
    {
        return self::$data->$key;
    }

    /**
     * @param mixed $value
     * @return array|object
     */
    protected static function arrayToObject($value)
    {
        if (is_array($value) && array_values($value) !== $value) {
            return (object) array_map("Jabberd2\\Config::arrayToObject", $value);
        } else {
            return $value;
        }
    }
} 