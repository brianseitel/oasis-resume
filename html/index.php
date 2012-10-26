<?
define('ROOT_DIR', realpath(dirname(dirname(__FILE__))));

require('../base/base.App.php');
ini_set('display_errors', 'On');
App::startup();
 
ob_start('ob_gzhandler');
$controller = fetch($_GET, 'controller');
$view = fetch($_GET, 'view');

echo render('layouts', 'application', array('controller' => $controller, 'view' => $view));
 
ob_end_flush();