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
class Auth {
	private static $app;
	private static $modelUser;
	private static $postUsername='';
	private static $postPassword='';
	private static $fieldUsername='';
	private static $fieldPassword='';
	private static $fieldLevel='';
	private static $info=[];

	private static $loginUrl='';

	public static function init(Kecik $app) {
		self::$app = $app;

		self::$modelUser = '\\Model\\'.$app->config->get('auth.model');
		self::$postUsername = $app->config->get('auth.post_username');
		self::$postPassword = $app->config->get('auth.post_password');

		self::$fieldUsername = $app->config->get('auth.field_username');
		self::$fieldPassword = $app->config->get('auth.field_password');
		self::$fieldLevel = $app->config->get('auth.field_level');
		self::$loginUrl = $app->config->get('auth.login_url');
		
		if (!empty(self::username())) {
			$model = self::$modelUser;
			$users = $model::find([
			'where' => [
					[self::$fieldUsername, '=', "'".self::username()."'"]
				]
			]);

			if (count($users) > 0) {
				self::$info = $users[0];
			} else {
				$app->url->redirect(self::$loginUrl);
			}
		} elseif ($app->route->is() != self::$loginUrl) { ?>
			<script type="text/javascript">
				document.location.href="<?php $app->url->to(self::$loginUrl) ?>";
			</script>
		<?php
		}
	}

	public static function login() {
		$model = self::$modelUser;
		$users = $model::find([
			'where' => [
				[self::$fieldUsername, '=', "'".$_POST[self::$postUsername]."'"],
				[self::$fieldPassword, '=', "'".md5($_POST[self::$postPassword])."'"]
			]
		]);

		if (count($users) > 0) {
			$_SESSION[md5('login'.self::$app->url->baseUrl())] = base64_encode('TRUE');
			$_SESSION[md5('username'.self::$app->url->baseUrl())] = base64_encode($_POST[self::$postUsername]);
			$level = self::$fieldLevel;
			foreach ($users as $user) {
				$_SESSION[md5('level'.self::$app->url->baseUrl())] = base64_encode($user->$level);
			}

			self::$app->url->redirect('');
		} else {
			self::$app->url->redirect(self::$loginUrl);
		}
	}

	public static function logout() {
		unset($_SESSION[md5('username'.self::$app->url->baseUrl())]);
		unset($_SESSION[md5('level'.self::$app->url->baseUrl())]);
		unset($_SESSION[md5('login'.self::$app->url->baseUrl())]);

		self::$app->url->redirect(self::$loginUrl);
	}

	public static function isLogin() {

		if (base64_decode($_SESSION[md5('login'.self::$app->url->baseUrl())]) == 'TRUE')
			return TRUE;
		else
			return FALSE;
	}

	public static function username() {
		if (isset($_SESSION[md5('username'.self::$app->url->baseUrl())]))
			return base64_decode($_SESSION[md5('username'.self::$app->url->baseUrl())]);
		else
			return '';
	}

	public static function level() {
		if (isset($_SESSION[md5('level'.self::$app->url->baseUrl())]))
			return base64_decode($_SESSION[md5('level'.self::$app->url->baseUrl())]);
		else
			return '';
	}

	public static function info($field) {
		if (isset(self::$info->$field))
			return self::$info->$field;
		else
			return '';
	}
}
