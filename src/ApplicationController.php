<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:37 AM
 */

namespace Jabberd2;


use Fabiang\Xmpp\Protocol\Message;

class ApplicationController extends Controller
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var Xmpp
     */
    protected $xmpp;

    /**
     * @param \PDO $pdo
     * @param Xmpp $xmpp
     */
    public function __construct(\PDO $pdo, Xmpp $xmpp)
    {
        $this->pdo = $pdo;
        $this->xmpp = $xmpp;
    }

    /**
     * @return Response
     */
    protected function executeIndex()
    {
        $view = new BufferedView();
        $view->setView($this->view);

        return new Response($view->run());
    }

    /**
     * @return RedirectResponse
     */
    protected function executeInstall()
    {
        $this->pdo->prepare("
                ALTER TABLE authreg
                    ADD alias VARCHAR(255)
            ")
            ->execute();

        return $this->redirectBack();
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function executeSendMessage()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $users = $this->get('users');
            $conferences = $this->get('conferences');
            $text = $this->get('text');

            if(empty($text)) {
                return new Response("Missing Users/Conferences/Text", 404);
            }

            if(!empty($users) || !empty($conferences)) {
                $client = $this->xmpp;
                $client->setOnline();

                // send to the users
                if(is_array($users)) {
                    foreach($users as $user) {
                        $client->sendMessage($text, $user);
                    }
                }

                // send to the conferences
                if(is_array($conferences)) {
                    foreach($conferences as $conference) {
                        $client->joinChannel($conference);
                        $client->sendMessage($text, $conference, Message::TYPE_GROUPCHAT);
                    }
                }
            }

            return $this->redirectBack();
        }

        $channels = $this->xmpp->getMucChannels();

        $view = new BufferedView();
        $view->setView($this->view);
        $view->setParameters(array(
                                  'channels' => $channels,
                                  'users' => $this->pdo->query("SELECT * FROM authreg")
                             ));

        return new Response($view->run());
    }

    /**
     * @return Response
     */
    protected function executeUsers()
    {
        $view = new BufferedView();
        $view->setView($this->view);
        $view->addParameter('users', $this->pdo->query("SELECT * FROM authreg"));

        return new Response($view->run());
    }

    /**
     * @return Response
     */
    protected function executeChangePass()
    {
        $username = $this->get('pk');
        $password = $this->get('value');

        if(empty($username) || empty($password)) {
            return new Response("Missing Username/Password", 404);
        }

        $stmt = $this->pdo->prepare("UPDATE authreg SET password=:password WHERE username=:username");
        $stmt->execute(array('username' => $username, 'password' => $password));

        return new Response('ok');
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function executeUserHide()
    {
        $username = $this->get('username');

        if(empty($username)) {
            return new Response("Missing Username", 404);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
        $stmt->execute(array('username' => $username));
        $actor = $stmt->fetch(\PDO::FETCH_ASSOC);
        unset($stmt);

        if(false === $actor) {
            return new Response("Missing Actor", 404);
        }

        $this->pdo->prepare("DELETE FROM `roster-items` WHERE `collection-owner`=:uid OR `jid`=:uid")
            ->execute(array('uid' => sprintf("%s@%s", $actor['username'], $actor['realm'])));

        return $this->redirectBack();
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function executeUserShare()
    {
        $username = $this->get('username');

        if(empty($username)) {
            return new Response("Missing Username", 404);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
        $stmt->execute(array('username' => $username));
        $actor = $stmt->fetch(\PDO::FETCH_ASSOC);
        unset($stmt);

        if(false === $actor) {
            return new Response("Missing Actor", 404);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username!=:username");
        $stmt->execute(array('username' => $username));
        $users = $stmt->fetchAll();
        unset($stmt);

        $this->pdo->beginTransaction();
        foreach($users as $user) {
            $stmt = $this->pdo->prepare("INSERT INTO `roster-items` (`collection-owner`, `jid`, `name`, `to`, `from`, `ask`) VALUES (:owner, :user, :alias, 1, 1, 0)");

            try {
                $stmt->execute(array('owner' => sprintf("%s@%s", $user['username'], $user['realm']), 'user' => sprintf("%s@%s", $actor['username'], $actor['realm']), 'alias' => $actor['alias']));
                $stmt->execute(array('user' => sprintf("%s@%s", $user['username'], $user['realm']), 'owner' => sprintf("%s@%s", $actor['username'], $actor['realm']), 'alias' => $user['alias']));
            } catch(\Exception $e) {
                $this->pdo->rollBack();
            }
        }
        $this->pdo->commit();

        return $this->redirectBack();
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function executeUserAdd()
    {
        $username = $this->get('username');
        $realm = $this->get('realm');
        $password = $this->get('password');
        $alias = $this->get('alias');

        if(empty($username) || empty($alias) || empty($realm) || empty($password)) {
            return new Response("Missing Username/Alias/Realm/Password", 404);
        }

        $this->pdo->prepare("INSERT INTO authreg (username, realm, password, alias) VALUES (:username, :realm, :password, :alias)")
            ->execute(array('username' => $username, 'realm' => $realm, 'password' => $password, 'alias' => $alias));

        $messages = Config::get('settings')->useradd->messages;

        // send initial messages
        if(is_array($messages) && !empty($messages)) {
            $client = $this->xmpp;
            $client->setOnline();

            // send messages
            foreach($messages as $message) {
                $client->sendMessage($message, sprintf("%s@%s", $username, $realm));
            }
        }

        return $this->redirectBack();
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function executeUserDelete()
    {
        $username = $this->get('username');

        if(empty($username)) {
            return new Response("Missing Username", 404);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
        $stmt->execute(array('username' => $username));
        $actor = $stmt->fetch(\PDO::FETCH_ASSOC);
        unset($stmt);

        if(false === $actor) {
            return new Response("Missing Actor", 404);
        }

        $this->pdo->prepare("DELETE FROM `roster-items` WHERE `collection-owner`=:uid OR `jid`=:uid")
            ->execute(array('uid' => sprintf("%s@%s", $actor['username'], $actor['realm'])));

        $this->pdo->prepare("DELETE FROM authreg WHERE username=:username")
            ->execute(array('username' => $username));

        return $this->redirectBack();
    }
} 