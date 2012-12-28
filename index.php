<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);

    require_once 'config/Constant.config.php';
    require_once 'config/Database.config.php';
    require_once 'config/Template.config.php';
    require_once 'config/Controller.config.php';
    require_once 'config/Permission.config.php';

    require_once 'core/interface/controller.interface.php';
    require_once 'core/interface/lib.interface.php';

    require_once 'core/Controller.class.php';
    require_once 'core/Mexception.class.php';
    require_once 'core/Controller.class.php';

    require_once 'lib/Path/Path.class.php';
    require_once 'lib/Permission/Permission.class.php';
    require_once 'lib/Query/Query.class.php';
    require_once 'lib/Database/MySQL.class.php';
    require_once 'lib/Template/Template.class.php';
    require_once 'lib/Helper/Helper.class.php';
    require_once 'lib/Form/Form.class.php';
    require_once 'lib/Session/Session.class.php';

    $Controller = new Core\Controller();
    $Controller->run();

?>
