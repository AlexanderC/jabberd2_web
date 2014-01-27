<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 2:25 PM
 */

namespace Jabberd2;

use Fabiang\Xmpp\Protocol\Roster;
use Fabiang\Xmpp\Protocol\Presence;
use Fabiang\Xmpp\Protocol\Message;
use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;

class Xmpp
{
    /**
     * @var \Fabiang\Xmpp\Client
     */
    protected $client;

    /**
     * @var \Fabiang\Xmpp\Options
     */
    protected $options;

    /**
     * @var string
     */
    protected $mucRoomsDir;

    /**
     * {@inherit}
     */
    public function __construct()
    {
        $config = Config::get('xmpp');

        $this->options = new Options($config->address);
        $this->options->setUsername($config->username)
            ->setPassword($config->password);

        $this->options->setLogger(new FileLogger(Config::get('settings')->log_dir));

        $this->client = new Client($this->options);

        $this->mucRoomsDir = rtrim($config->mucdir, "/") . "/";
    }

    /**
     * @return string
     */
    public function getMucRoomsDir()
    {
        return $this->mucRoomsDir;
    }

    /**
     * @return \Fabiang\Xmpp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return \Fabiang\Xmpp\Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return void
     */
    public function setOnline()
    {
        $this->client->connect();
        $this->client->send(new Roster());
        $this->client->send(new Presence());
    }

    /**
     * Channel:
     * ex. channelname@conference.myjabber.com
     *
     * @param string $name
     * @param bool|string $alias
     * @return Presence
     */
    public function joinChannel($name, $alias = false)
    {
        $channel = new Presence();
        $channel->setTo($name)
            ->setNickName($alias ? : Config::get('xmpp')->alias);

        $this->client->send($channel);

        return $channel;
    }

    /**
     * Tt:
     * ex. nickname@myjabber.com
     *     channelname@conference.myjabber.com (when channel only)
     *
     * @param string $text
     * @param string $to
     * @param string $type
     */
    public function sendMessage($text, $to, $type = Message::TYPE_CHAT)
    {
        $message = new Message();

        $message->setMessage($text)
            ->setTo($to)
            ->setType($type);

        $this->client->send($message);
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getMucChannels()
    {
        $file = sprintf("%srooms.xml", $this->mucRoomsDir);

        if(!is_file($file) || false === ($content = file_get_contents($file, LOCK_NB))) {
            throw new \RuntimeException("Unable to open muc rooms DB file");
        }

        $data = $this->parseXml($content);
        unset($content);

        $channels = array();

        if(isset($data['registered']['item'])) {
            $data = $data['registered']['item'];

            foreach($data as $channel) {
                $channels[] = $channel['@attributes'];
            }
        }

        return $channels;
    }

    /**
     * @param string $xml
     * @return array
     * @throws \RuntimeException
     */
    protected function parseXml($xml)
    {
        if(!function_exists('simplexml_load_string')) {
            throw new \RuntimeException("Missing SimpleXml php extension");
        }

        $object = simplexml_load_string($xml);

        // also apply hook to transform it to array
        return json_decode(json_encode($object), true);
    }

    /**
     * {@inherit}
     */
    public function __destruct()
    {
        if(is_object($this->client)) {
            $this->client->disconnect();
        }
    }
} 