<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    require_once 'config/constant.config.php';
    require_once 'core/Controller.class.php';
    require_once 'core/Bootstrap.class.php';
    require_once 'core/Mexception.class.php';
    require_once 'core/Controller.class.php';
    require_once 'lib/Helper/Helper.class.php';
    require_once 'lib/MySQL/MySQL.class.php';
    require_once 'lib/Query/Query.class.php';
    require_once 'lib/Session/Session.class.php';
    require_once 'lib/Form/Form.class.php';
    require_once 'lib/Template/Template.class.php';

    $Bootstrap = new Core\Bootstrap();
    $Bootstrap->registerApplication('Database', 'MySQL', '\Lib');

    $Controller = new Core\Controller($Bootstrap);
    $Controller->run();

?>
