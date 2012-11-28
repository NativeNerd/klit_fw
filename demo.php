<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    define('MEXCEPTION_ELEVEL', 9);

    require_once 'inc/Bootstrap.class.php';
    require_once 'inc/Mexception.class.php';
    require_once 'inc/Helper.class.php';
    require_once 'inc/Database.class.php';
    require_once 'inc/Model.class.php';
    require_once 'inc/Session.class.php';
    require_once 'inc/Form.class.php';
    require_once 'inc/Template.class.php';
    require_once 'inc/Form.class.php';

    $Bootstrap = new Bootstrap();


    /* --------------------------------------- */
    $Database = new Database('127.0.0.1', 'root', 'root', 'test');
    $Bootstrap->openApplication($Database, 'Database');

    $Model = new Model($Bootstrap);
    $Bootstrap->openApplication($Model, 'Model');

    $Template = new Template($Bootstrap, 'templates');
    $Bootstrap->openApplication($Template, 'Template');

    $Session = new Session($Bootstrap);
    $Bootstrap->openApplication($Session, 'Session');

    $Form = new FormNew($Bootstrap);
    $Bootstrap->openApplication($Form, 'Form');

    $Form->openForm('AddUser', 'demo.php', 'POST');

    $Form->addInput('text', 'testfeld', 'leer');
    $Form->addLabel('testfeld', 'Label');
    $Form->addInput('submit', 'submit', 'Weiter');

    $Template->open('index.tpl');
    $Template->assign('test1', array('Erster', 'Zweiter', 'Dritter'));
    $Template->assign('test2', 'Wert 1');
    $Template->assign('variable', array('index'=>array('index_2'=>array('index_3'=>'Inhalt'))));
    echo $Template->parse();


    /* --------------------------------------- */
?>
