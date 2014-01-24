<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:05 AM
 */

namespace Jabberd2;


class Response
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $code;

    /**
     * @param string $content
     * @param int $code
     */
    public function __construct($content = null, $code = 200)
    {
        $this->content = $content;
        $this->code = (int) $code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = (int) $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return void
     */
    public function flush()
    {
        @ob_end_clean();
        http_response_code($this->code);
        exit($this->content);
    }
} 