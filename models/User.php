<?php

require_once '../models/Database.php';
class User
{
	private $db;

	public function __construct()
	{
		$this->db = Database::getInstance()->getConnection();
	}

	public function login($email, $password)
	{
		$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
		$stmt->execute(['email' => $email]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['user_id'] = $user['id'];
			return true;
		}
		return false;
	}
}
