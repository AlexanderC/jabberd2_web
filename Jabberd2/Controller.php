<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:04 AM
 */

namespace Jabberd2;


class Controller
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $request;

    /**
     * @return RedirectResponse
     */
    protected function redirectBack()
    {
        $response = new RedirectResponse();
        $response->setUrl($_SERVER['HTTP_REFERER']);

        return $response;
    }

    /**
     * @param string $name
     * @param bool $trim
     * @return bool|string
     */
    protected function get($name, $trim = true)
    {
        return isset($this->request[$name]) ? ($trim ? trim($this->request[$name]) : $this->request[$name]) : false;
    }

    /**
     * @param string $action
     * @return Response
     * @throws \InvalidResponseObjectException
     */
    public function execute($action)
    {
        $method = sprintf("execute%s", ucfirst($action));

        if(!method_exists($this, $method)) {
            return new Response("Missing required method", 404);
        } else {
            $this->request = $_REQUEST;
            $this->view = strtolower($action);

            $response = $this->$method();

            if(!($response instanceof Response)) {
                throw new \InvalidResponseObjectException("Invalid Response object provided");
            }

            return $response;
        }
    }
} 