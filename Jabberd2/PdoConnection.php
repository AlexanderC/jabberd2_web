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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->connection = new \PDO(
            sprintf(
                self::DSL_TPL,
                $config['db']['name'],
                $config['db']['host']
            ),
            $config['db']['user'], $config['db']['pass']
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