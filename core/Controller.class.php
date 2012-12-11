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

        /**
         * Initializes the class
         * @param \Core\Bootstrap $Bootstrap
         */
        public function __construct(\Core\Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
            $this->Bootstrap->openApplication($this, 'Controller');
            require_once 'interface/controller.interface.php';
        }

        /**
         * Runs the application
         * @throws \Core\Mexception
         */
        public function run() {
            try {
                $uri = \Lib\Helper::parseUri();
                if (!$uri) {
                    $uri['main'] = \Config\Controller::DEFAULT_CONTROLLER;
                    $uri['action'] = \Config\Controller::DEFAULT_ACTION;
                    $uri['do'] = \Config\Controller::DEFAULT_DO;
                }
                $path = \Lib\Helper::buildPath('src/controller/'.$uri['main'].'/'.$uri['main'].'.controller.php');
                if (file_exists($path)) {
                    require_once $path;
                } else {
                    throw new \Core\Mexception('Unknown controller');
                }
                $class = '\Src\Controller\\'.$uri['main'];
                $Controller = new $class($this->Bootstrap);
                if (!in_array('Core\Implement\controller', class_implements($Controller, false))) {
                    throw new \Core\Mexception('Controller does not implement interface');
                }
                if (method_exists($Controller, $uri['action'])) {
                    $action = $uri['action'];
                    $Controller->$action($uri);
                } else {
                    throw new \Core\Mexception('Unknown action');
                }
            } catch (\Core\Mexception $E) {
                $E->quit($E->getMessage());
            }
        }

        /**
         * Closes the class
         */
        public function __desctruct() {

        }

    }

?>
