<?php
    namespace Core;
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
    class Controller {
        protected $Bootstrap;
        protected $Path;

        /**
         * Initializes the class
         * @param \Core\Bootstrap $Bootstrap
         */
        public function __construct(\Core\Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
            $this->Bootstrap->openApplication($this, 'Controller');
            $this->Path = $this->Bootstrap->getApplication('Path');
            require_once 'interface/controller.interface.php';
        }

        /**
         * Runs the application
         * @throws \Core\Mexception
         */
        public function run() {
            try {
                $uri = $this->Path->parseUri();
                if (!$uri) {
                    $uri['main'] = \Config\Controller::DEFAULT_CONTROLLER;
                    $uri['action'] = \Config\Controller::DEFAULT_ACTION;
                }
                $path = $this->Path->buildPath('src/controller/'.$uri['main'].'/'.$uri['main'].'.controller.php');
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
