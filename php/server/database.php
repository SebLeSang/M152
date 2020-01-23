<?php
/**
 * @author 	dominique.aigroz@edu.ge.ch
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/m152/config/conparam.php';

/**
 * @brief	Helper class pour encapsuler l'objet PDO
 * 			et l'appel aux méthodes
 * @author 	dominique.aigroz@edu.ge.ch
 * @remark	
 *  utilisation : 
 *  EDatabase::prepare('SELECT USERID, EMAIL, USERNAME FROM USER', array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
 */
class EDatabase {
	private static $pdoInstance;
	/**
	 * @brief	Class Constructor - Create a new database connection if one doesn't exist
	 * 			Set to private so no-one can create a new instance via ' = new KDatabase();'
	 */
	private function __construct() {}
	/**
	 * @brief	Like the constructor, we make __clone private so nobody can clone the instance
	 */
	private function __clone() {}
	/**
	 * @brief	Returns DB instance or create initial connection
	 * @return $objInstance;
	 */
	public static function getInstance() {
		if(!self::$pdoInstance){
			try{
				$dsn = EDB_DBTYPE.':host='.EDB_HOST.';port='.EDB_PORT.';dbname='.EDB_DBNAME;
				self::$pdoInstance = new PDO($dsn, EDB_USER, EDB_PASS, array('charset'=>'utf8'));
				self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException  $e ){
				echo "EDatabase Error: ".$e->getMessage();
			}
		}
		return self::$pdoInstance;
	} # end method

	/**
	 * @brief	Passes on any static calls to this class onto the singleton PDO instance
	 * @param 	$chrMethod		The method to call
	 * @param 	$arrArguments	The method's parameters
	 * @return 	$mix			The method's return value
	 */
	final public static function __callStatic( $chrMethod, $arrArguments ) {
		$pdo = self::getInstance();
		return call_user_func_array(array($pdo, $chrMethod), $arrArguments);
	} # end method
}
