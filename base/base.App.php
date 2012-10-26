<?

class App {

	public static function auto_load($class_name) {
		if (is_file(MODEL_PATH.'/model.'.$class_name.'.php'))
			require(MODEL_PATH.'/model.'.$class_name.'.php');
        elseif (is_file(CLASS_PATH.'/class.'.$class_name.'.php'))
            require(CLASS_PATH.'/class.'.$class_name.'.php');
	}

    public static function startup() {
        define('BASE_PATH', ROOT_DIR.'/base');
        define('CLASS_PATH', ROOT_DIR.'/classes');
        define('MODEL_PATH', ROOT_DIR.'/models');
        define('CONTROLLER_PATH', ROOT_DIR.'/controllers');
        define('VIEW_PATH', ROOT_DIR.'/views');
    
        require_once(ROOT_DIR.'/../bookpass.php');
        require_once(BASE_PATH.'/base.Controller.php');
        require_once(BASE_PATH.'/base.DB.php');
        require_once(BASE_PATH.'/base.Object.php');
        require_once(BASE_PATH.'/base.Util.php');

		spl_autoload_register(Array('self', 'auto_load'));

        new Controller();
    }
}