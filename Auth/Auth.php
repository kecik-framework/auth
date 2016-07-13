<?php
/*///////////////////////////////////////////////////////////////
 /** ID: | /-- ID: Indonesia
 /** EN: | /-- EN: English
 ///////////////////////////////////////////////////////////////*/

/**
 * ID: Cookie - Library untuk framework kecik, library ini khusus untuk membantu masalah Cookie
 * EN: Cookie - Library for Kecik Framework, this library specially for help Cookie problem
 *
 * @author        Dony Wahyu Isp
 * @copyright    2015 Dony Wahyu Isp
 * @link        http://github.com/kecik-framework/cookie
 * @license        MIT
 * @version    2.0.1-alpha
 * @package        Kecik\Cookie
 **/
namespace Kecik;

/**
 * Cookie
 * @package    Kecik\Cokie
 * @author        Dony Wahyu Isp
 * @since        1.0.0-alpha
 **/
class Auth {
	private static $app;
	private static $modelUser;
	private static $postUsername  = '';
	private static $postPassword  = '';
	private static $fieldUsername = '';
	private static $fieldPassword = '';
	private static $fieldLevel    = '';
	private static $info          = array();

	private static $loginRoute    = 'login';
	private static $logoutRoute   = 'logout';
	private static $loginTemplate = 'login';

	private static $encryptFunction = NULL;

	/**
	 * Initialize for Auth Class
	 */
	public static function init() {

		self::$app = Kecik::getInstance();;

		self::$modelUser    = '\\Model\\' . self::$app->config->get( 'auth.model' );
		self::$postUsername = self::$app->config->get( 'auth.post_username' );
		self::$postPassword = self::$app->config->get( 'auth.post_password' );

		self::$fieldUsername   = self::$app->config->get( 'auth.field_username' );
		self::$fieldPassword   = self::$app->config->get( 'auth.field_password' );
		self::$fieldLevel      = self::$app->config->get( 'auth.field_level' );
		self::$loginRoute      = ( self::$app->config->get( 'auth.login_route' ) != '' ) ? self::$app->config->get( 'auth.login_route' ) : self::$loginRoute;
		self::$logoutRoute     = ( self::$app->config->get( 'auth.logout_route' ) != '' ) ? self::$app->config->get( 'auth.logout_route' ) : self::$logoutRoute;
		self::$loginTemplate   = ( self::$app->config->get( 'auth.login_template' ) != '' ) ? self::$app->config->get( 'auth.login_template' ) : self::$loginTemplate;
		self::$encryptFunction = ( self::$app->config->get( 'auth.encrypt_function' ) != '' ) ? self::$app->config->get( 'auth.encrypt_function' ) : self::$encryptFunction;

		if ( ! empty( self::username() ) ) {
			$model = self::$modelUser;
			$users = $model::find( [
				'where' => [
					[ self::$fieldUsername, '=', self::username() ]
				]
			] );

			if ( count( $users ) > 0 ) {
				self::$info = $users[0];
			}
		}

		AuthInit( self::$app );
	}

	/**
	 * Get current username
	 *
	 * @return string
	 */
	public static function username() {
		if ( isset( $_SESSION[ md5( 'username' . self::$app->url->baseUrl() ) ] ) ) {
			return base64_decode( $_SESSION[ md5( 'username' . self::$app->url->baseUrl() ) ] );
		} else {
			return '';
		}
	}

	/**
	 * Login action
	 */
	public static function login() {
		$model = self::$modelUser;

		if ( is_callable( self::$encryptFunction ) ) {
			$encryptFunction = self::$encryptFunction;
			$password        = $encryptFunction( $_POST[ self::$postPassword ] );
		} else {
			$password = md5( $_POST[ self::$postPassword ] );
		}
		$users = $model::find( [
			'where' => [
				[ self::$fieldUsername, '=', $_POST[ self::$postUsername ] ],
				[ self::$fieldPassword, '=', $password ]
			]
		] );

		if ( count( $users ) > 0 ) {
			$_SESSION[ md5( 'login' . self::$app->url->baseUrl() ) ]    = base64_encode( 'TRUE' );
			$_SESSION[ md5( 'username' . self::$app->url->baseUrl() ) ] = base64_encode( $_POST[ self::$postUsername ] );
			$level                                                      = self::$fieldLevel;
			foreach ( $users as $user ) {
				$_SESSION[ md5( 'level' . self::$app->url->baseUrl() ) ] = base64_encode( $user->$level );
			}
		}

		if ( strtolower( substr( $_SERVER["HTTP_REFERER"], - ( strlen( 'login' ) ) ) ) === 'login' ) {
			self::$app->url->redirect( '' );
		} else {
			header( 'Location: ' . $_SERVER["HTTP_REFERER"] );
			exit();
		}
	}

	/**
	 * Logout action
	 */
	public static function logout() {
		unset( $_SESSION[ md5( 'username' . self::$app->url->baseUrl() ) ] );
		unset( $_SESSION[ md5( 'level' . self::$app->url->baseUrl() ) ] );
		unset( $_SESSION[ md5( 'login' . self::$app->url->baseUrl() ) ] );

		self::$app->url->redirect( self::$loginRoute );
	}

	/**
	 * Check of login status
	 * @return bool
	 */
	public static function isLogin() {

		if ( isset( $_SESSION[ md5( 'login' . self::$app->url->baseUrl() ) ] ) &&
		     base64_decode( $_SESSION[ md5( 'login' . self::$app->url->baseUrl() ) ] ) == 'TRUE'
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get current level from user logged
	 *
	 * @return string
	 */
	public static function level() {
		if ( isset( $_SESSION[ md5( 'level' . self::$app->url->baseUrl() ) ] ) ) {
			return base64_decode( $_SESSION[ md5( 'level' . self::$app->url->baseUrl() ) ] );
		} else {
			return '';
		}
	}

	/**
	 * Get another info from current user logged
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public static function info( $field ) {
		if ( isset( self::$info->$field ) ) {
			return self::$info->$field;
		} else {
			return '';
		}
	}

	/**
	 * Get Login route
	 *
	 * @return string
	 */
	public static function loginRoute() {
		return self::$loginRoute;
	}

	/**
	 * Get Logout route
	 *
	 * @return string
	 */
	public static function logoutRoute() {
		return self::$logoutRoute;
	}

	/**
	 * Get Login template
	 *
	 * @return string
	 */
	public static function loginTemplate() {
		return self::$loginTemplate;
	}
}


/**
 * Initialize Auth module
 * @param $app
 */
function AuthInit( $app ) {
	/**
	 * Create login route
	 */
	$app->get( Auth::loginRoute(), function () {
		if ( Auth::isLogin() ) {
			$this->url->redirect( '' );
		}
	} )->template( Auth::loginTemplate() );

	/**
	 * Create action login route
	 */
	$app->post( Auth::loginRoute(), function () {
		Auth::login();
	} );

	/**
	 * Create logout route
	 */
	$app->get( Auth::logoutRoute(), function () {
		Auth::logout();
	} );
}