<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    require_once 'config/Constant.config.php';
    require_once 'core/Autoloader.class.php';
    require_once 'lib/template/Twig/Autoloader.php';
    spl_autoload_register(array('\Core\Autoloader', 'load'));
    Twig_Autoloader::register();

    $Controller = new Core\Controller();
    $Controller->run();

?>
