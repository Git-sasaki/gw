<?php
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Http\ServerRequest;
use Cake\Http\Response;

/**
 * Class UserAuthenticate
 * @package App\Auth
 */
class UserAuthenticate extends BaseAuthenticate{

	/**
	 * @param ServerRequest $request
	 * @param Response      $response
	 *
	 * @return array|bool|mixed
	 */
	public function authenticate(ServerRequest $request, Response $response){
		$userTable = $this->getTableLocator()->get('Users');

		$user = $userTable->query()
			->where([
				'Users.user' => $request->getData('user', ''),
				'Users.password' => $request->getData('password', ''),
			])
			->first();

		if ($user) {
			return $user->toArray();
		}

		return false;
	}
}