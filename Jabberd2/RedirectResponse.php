<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:24 AM
 */

namespace Jabberd2;


class RedirectResponse extends Response
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     * @throws \InvalidArgumentException
     */
    public function setUrl($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid url provided");
        }

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inherit}
     */
    public function flush()
    {
        @ob_end_clean();
        header(sprintf('Location: %s', $this->url));
        exit;
    }

} 