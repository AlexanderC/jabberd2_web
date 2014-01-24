<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:58 AM
 */

namespace Jabberd2;


class PdoConnection
{
    const DSL_TPL = "mysql:dbname=%s;host=%s";

    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * {@inherit}
     */
    public function __construct()
    {
        $config = Config::get('db');

        $this->connection = new \PDO(
            sprintf(
                self::DSL_TPL,
                $config->name,
                $config->host
            ),
            $config->user, $config->pass
        );
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
} 