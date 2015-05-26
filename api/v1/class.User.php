<?php

require_once 'class.API.php';

class User extends API
{

	public function __construct($request, $origin) {
		parent::__construct($request);

		$this->methods = array(
			'register' => 'POST',
			'userExists' => 'GET',
			'login' => 'POST',
			'logout' => 'POST',
			'loggedInUser' => 'GET',
			'resetPassword' => 'POST'
			);
	}

	private function hashPassword($password) {
		return $password;
	}

	private function generateToken($seed=1) {
		return $seed;
	}

	protected function register() {
		$requestData = array(
			'user_email' => $this->requestArray['user_email'],
			'user_password' => $this->hashPassword($this->requestArray['user_password']),
			'user_level' => '0'
			);
		$result = $this->userExists($requestData['user_email']);
		if (!$result) {
			$result = $this->oMySQL->insert('users', $requestData);
			return $result;
		} else {
			return false;
		}
	}

	protected function userExists() {
		$requestData = array(
			'user_email' => $this->requestArray['user_email']
			);
		$result = $this->oMySQL->select('users', $requestData);
		return (count($result) > 0);
	}

	protected function login() {
		$requestData = array(
			'user_email' => $this->requestArray['user_email'],
			'user_password' => $this->hashPassword($this->requestArray['user_password'])
			);
		$result = $this->oMySQL->select('users', $requestData);
		if (count($result) == 1) {
			$_SESSION = array(
				'user_id' => $result[0]['user_id']
				);
			return true;
		}
		return false;
	}

	protected function logout() {
		$_SESSION = array();
		return true;
	}

	protected function loggedInUser() {
		return $_SESSION['user_id'];
	}

	private function resetPassword_notoken() {
		$token = $this->generateToken();
		$selectData = array(
			'user_email' => $this->requestArray['user_email']
			);
		$updateData = array(
			'user_token' => $token
			);
		$result = $this->oMySQL->update('users', $updateData, $selectData);
		if ($result) {
			return $token;
		}
		return false;
	}
	private function resetPassword_token() {
		$selectData = array(
			'user_email' => $this->requestArray['user_email'],
			'user_token' => $this->requestArray['user_token']
			);
		$result = (count($this->oMySQL->select('users', $selectData)) == 1);
		if ($result) {
			$updateData = array(
				'user_password' => $this->hashPassword($this->requestArray['user_password']),
				'user_token' => null
			);
			$result = $this->oMySQL->update('users', $updateData, $selectData);
			return $result;
		}
		return false;
	}
	protected function resetPassword() {
		if ($this->requestArray['user_token'] == null) {
			return $this->resetPassword_notoken();
		} else {
			return $this->resetPassword_token();
		}
	}
}

?>
