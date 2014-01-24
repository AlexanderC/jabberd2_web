<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:37 AM
 */

namespace Jabberd2;


class ApplicationController extends Controller
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
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
        $this->pdo->prepare("ALTER TABLE authreg ADD alias VARCHAR(255)")
            ->execute();

        return $this->redirectBack();
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