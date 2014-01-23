<?php

class Controller 
{
	protected $pdo;
	protected $viewTpl;
	protected $view;
	protected $request;

	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->viewTpl = __DIR__ . "/views/%s.php";
	}

	protected function executeIndex()
	{
		include $this->view;
	}

	protected function executeInstall()
	{
		$this->pdo->prepare("ALTER TABLE authreg ADD alias VARCHAR(255)")
			->execute();
		
		$this->redirectBack();
	}
	
	protected function executeUsers()
	{
		$users = $this->pdo->query("SELECT * FROM authreg");

		include $this->view;
	}

	protected function executeUserHide()
	{
		$username = $this->get('username');

                if(empty($username)) {
                        echo "Missing Username";
                        return;
                }

		$stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
                $stmt->execute(array('username' => $username));
                $actor = $stmt->fetch(\PDO::FETCH_ASSOC);

		if(false === $actor) {
                        echo "No Such User Found";
                        return;
                }

		$this->pdo->prepare("DELETE FROM `roster-items` WHERE `collection-owner`=:uid OR `jid`=:uid")
			->execute(array('uid' => sprintf("%s@%s", $actor['username'], $actor['realm'])));

		$this->redirectBack();
	}

	protected function executeUserShare()
	{
		$username = $this->get('username');

                if(empty($username)) {
                        echo "Missing Username";
                        return;
                }

		$stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
		$stmt->execute(array('username' => $username));
		$actor = $stmt->fetch(\PDO::FETCH_ASSOC);

		if(false === $actor) {
			echo "No Such User Found";
			return;
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

		$this->redirectBack();
	}

	protected function executeUserAdd()
	{
		$username = $this->get('username');
		$realm = $this->get('realm');
		$password = $this->get('password');
		$alias = $this->get('alias');

		if(empty($username) || empty($alias) || empty($realm) || empty($password)) {
			echo "Missing Username/Alias/Realm/Password";
			return;
		}

		$this->pdo->prepare("INSERT INTO authreg (username, realm, password, alias) VALUES (:username, :realm, :password, :alias)")
			->execute(array('username' => $username, 'realm' => $realm, 'password' => $password, 'alias' => $alias));

		$this->redirectBack();
	}

	protected function executeUserDelete()
	{
		$username = $this->get('username');

		if(empty($username)) {
			echo "Missing Username";
			return;
		}

		$stmt = $this->pdo->prepare("SELECT * FROM authreg WHERE username=:username");
                $stmt->execute(array('username' => $username));
                $actor = $stmt->fetch(\PDO::FETCH_ASSOC);

                if(false === $actor) {
                        echo "No Such User Found";
                        return;
                }

		$this->pdo->prepare("DELETE FROM `roster-items` WHERE `collection-owner`=:uid OR `jid`=:uid")
                        ->execute(array('uid' => sprintf("%s@%s", $actor['username'], $actor['realm'])));

		$this->pdo->prepare("DELETE FROM authreg WHERE username=:username")
			->execute(array('username' => $username));

		$this->redirectBack();
	}

	private function redirectBack()
	{
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
	}

	private function get($name, $trim = true)
	{
		return isset($this->request[$name]) ? ($trim ? trim($this->request[$name]) : $this->request[$name]) : false;
	}

	public function execute($action)
	{
		$method = sprintf("execute%s", ucfirst($action));
		$view = strtolower($action);

		if(!method_exists($this, $method)) {
			echo "Missing Required Action";
		} else {
			$this->request = $_REQUEST;
			$this->view = sprintf($this->viewTpl, $view);

			$this->$method();
		}
	}
}
