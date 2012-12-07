<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    require_once 'config/Constant.config.php';
    require_once 'config/Database.config.php';
    require_once 'config/Template.config.php';
    require_once 'config/Controller.config.php';

    require_once 'core/Controller.class.php';
    require_once 'core/Bootstrap.class.php';
    require_once 'core/Mexception.class.php';
    require_once 'core/Controller.class.php';
    require_once 'lib/Helper/Helper.class.php';

    $Bootstrap = new Core\Bootstrap();
    $Bootstrap->registerApplication('Query', 'Query', '\Lib');
    $Bootstrap->registerApplication('Database', 'MySQL', '\Lib');
    $Bootstrap->registerApplication('Form', 'Form', '\Lib');
    $Bootstrap->registerApplication('Template', 'Template', '\Lib');
    $Bootstrap->registerApplication('Session', 'Session', '\Lib');
    $Bootstrap->registerApplication('Crypto', 'Crypto', '\Lib');

    $Controller = new Core\Controller($Bootstrap);
    $Controller->run();

?>
