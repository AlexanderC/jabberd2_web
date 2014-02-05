<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 2/5/14
 * @time 11:40 AM
 */

namespace Jabberd2;


class ViewBuffer
{
    /**
     * @var string
     */
    protected $layout;

    /**
     * @var array
     */
    protected $vars = array();

    /**
     * @var string
     */
    protected $dump;

    /**
     * @var string
     */
    protected $regex = "/%\S+%/u";

    /**
     * @param string $layout
     */
    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param string $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @return void
     */
    public function listen()
    {
        @ob_end_flush();
        ob_start();
    }

    /**
     * @param string $var
     * @throws \RuntimeException
     */
    public function collect($var)
    {
        if(!ob_get_level()) {
            throw new \RuntimeException("You may run listen() first");
        }

        $this->vars[$var] = ob_get_clean();
    }

    /**
     * @return string
     */
    public function getDump()
    {
        return $this->dump;
    }

    /**
     * @return void
     */
    public function dump()
    {
        $keys = array_map(function($value) {
                return "%{$value}%";
            }, array_keys($this->vars));

        @ob_end_flush();
        ob_start();
        require($this->layout);
        $layoutContent = ob_get_clean();

        $this->dump = preg_replace(
            $this->regex,
            "",
            str_replace($keys, array_values($this->vars), $layoutContent)
        );

        return $this->dump;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dump ? : $this->dump();
    }
} 