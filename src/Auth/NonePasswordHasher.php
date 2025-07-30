<?php
/**
 * Login用コントローラー
 * @author hideki
 *
 */

namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;


class NonePasswordHasher extends AbstractPasswordHasher {
	protected $_config = array('hashType' => null);

	public function hash($password) {
		return $password;
	}

	public function check($password, $hashedPassword) {
		//$hashedPassword 認証フォームで入力されたパスワード
		//$hashedPassword DBに登録してあるpassword
		return $hashedPassword === $password;
	}
}