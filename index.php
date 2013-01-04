<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    require_once 'config/Constant.config.php';
    require_once 'core/Autoloader.class.php';
    spl_autoload_register(array('\Core\Autoloader', 'load'), true, true);

    $Controller = new Core\Controller();
    $Controller->run();

?>
