<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * ID: Cookie - Library untuk framework kecik, library ini khusus untuk membantu masalah Cookie 
 * EN: Cookie - Library for Kecik Framework, this library specially for help Cookie problem
 *
 * @author 		Dony Wahyu Isp
 * @copyright 	2015 Dony Wahyu Isp
 * @link 		http://github.com/kecik-framework/cookie
 * @license		MIT
 * @version 	1.0.1-alpha
 * @package		Kecik\Cookie
 **/
namespace Kecik;

/**
 * Cookie
 * @package 	Kecik\Cokie
 * @author 		Dony Wahyu Isp
 * @since 		1.0.0-alpha
 **/
class AuthController extends Controller {
	public function __construct(Kecik $app) {
		if (!Auth::isLogin())
			$app->template(Auth::loginTemplate(), TRUE);
	}

	public function login() {
		Auth::login();
	}

	public function logout() {
		Auth::logout();
	}
}