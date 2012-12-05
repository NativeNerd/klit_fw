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
        }

        public function run() {
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
                throw new Mexception('Unknown action');
            }
            return true;
        }

        public function __desctruct() {

        }

    }

?>
