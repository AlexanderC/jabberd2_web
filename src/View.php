<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:10 AM
 */

namespace Jabberd2;


use Jabberd2\Exception\MissingViewException;

class View
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var string
     */
    protected $viewTpl;

    /**
     * @var string
     */
    protected $layout = "layout";

    /**
     * {@inherit}
     */
    public function __construct()
    {
        $this->viewTpl = __DIR__ . "/../views/%s.php";
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
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @throws Exception\MissingViewException
     */
    public function run()
    {
        $viewFile = sprintf($this->viewTpl, $this->view);
        $layoutFile = sprintf($this->viewTpl, $this->layout);

        if(!is_file($viewFile) || !is_readable($viewFile)
            || !is_file($layoutFile) || !is_readable($layoutFile)) {
            throw new MissingViewException(
                "Missing view [{$this->view}]: {$viewFile} OR layout [{$this->layout}]: {$layoutFile}"
            );
        }

        foreach($this->getParameters() as $var => $value) {
            $$var = $value;
        }

        $buffer = new ViewBuffer($layoutFile);
        require($viewFile);
    }

    /**
     * {@inherit}
     */
    public function __toString()
    {
        return (string) $this->run();
    }
} 