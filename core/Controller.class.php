<?php

    /**
     * [Controller.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Handles the application
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    namespace Core;
    class Controller {
        private $Bootstrap;

        public function __construct(\Core\Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
            $this->Bootstrap->openApplication($this, 'Controller');
            require_once 'interface/controller.interface.php';
        }

        public function run() {
            try {
                $uri = \Lib\Helper::parseUri();
                $path = \Lib\Helper::buildPath('src/controller/'.$uri['main'].'/'.$uri['main'].'.controller.php');
                if (file_exists($path)) {
                    require_once $path;
                } else {
                    throw new \Core\Mexception('Unknown controller');
                }
                $class = '\Src\Controller\\'.$uri['main'];
                $Controller = new $class($this->Bootstrap);
                if (method_exists($Controller, $uri['action'])) {
                    $action = $uri['action'];
                    $Controller->$action($uri);
                } else {
                    throw new \Core\Mexception('Unknown action');
                }
            } catch (\Core\Mexception $E) {
                $path = \Lib\Helper::buildPath('src/controller/'
                    . \Config\Controller::DEFAULT_CONTROLLER
                    . '/'
                    . \Config\Controller::DEFAULT_CONTROLLER
                    . '.controller.php');
                if (file_exists($path)) {
                    require_once $path;
                } else {
                    throw new \Core\Mexception('Unable to load default controller');
                }
                $class = '\Src\Controller\\' . \Config\Controller::DEFAULT_CONTROLLER;
                $Controller = new $class($this->Bootstrap);
                if (method_exists($Controller, \Config\Controller::DEFAULT_ACTION)) {
                    $action = \Config\Controller::DEFAULT_ACTION;
                    $Controller->$action(false);
                } else {
                    throw new \Core\Mexception('Unable to execute default action');
                }
            }
        }

        public function __desctruct() {

        }

    }

?>
