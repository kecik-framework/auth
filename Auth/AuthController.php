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
	protected $request = '';
	protected $url = '';
	protected $assets = '';
	protected $config = '';

	public function __construct(Kecik $app) {
		if (!Auth::isLogin())
			$app->template(Auth::loginTemplate(), TRUE);

		$this->request = $app->request;
		$this->url = $app->url;
		$this->assets = $app->assets;
		$this->config = $app->config;
		if (isset($app->container))
			$this->container = $app->container;
		if (isset($app->db))
			$this->db = $app->db;
		if (isset($app->session))
			$this->session = $app->session;
		if (isset($app->cookie))
			$this->cookie = $app->cookie;
		if (isset($app->language))
			$this->language = $app->language;
	}

	public function login() {
		Auth::login();
	}

	public function logout() {
		Auth::logout();
	}
}