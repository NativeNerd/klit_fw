<?php
    namespace Core;
    /**
     * [Controller.class.php]
     * @version 1.0.0
     * @revision 01
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
        public function __construct() {
            $this->Path = \Lib\Path\Path::getInstance();
        }

        /**
         * Runs the application
         * @throws \Core\Mexception
         */
        public function run() {
            $uri = $this->Path->parseUri();
            if (!$uri) {
                $uri['main'] = \Config\Controller::DEFAULT_CONTROLLER;
                $uri['action'] = \Config\Controller::DEFAULT_ACTION;
            }
            $path = $this->Path->buildPath(\Config\Constant::PATH_CONTROLLER
                . $uri['main']
                . '/'
                . $uri['main']
                . \Config\Constant::FILE_CONTROLLEREXT);
            $path_steady = $this->Path->buildPath(\Config\Constant::PATH_CONTROLLER
                . \Config\Controller::STEADY_CONTROLLER
                . '/'
                . \Config\Controller::STEADY_CONTROLLER
                . \Config\Constant::FILE_CONTROLLEREXT);
            if (file_exists($path) AND file_exists($path_steady)) {
                require_once $path;
            } else {
                throw new \Core\Mexception('Unknown controller or steady');
            }
            $class = '\Src\Controller\\'.ucfirst($uri['main']);
            $class_steady = '\Src\Controller\\'.ucfirst(\Config\Controller::STEADY_CONTROLLER);
            $Controller = new $class();
            $Steady = new $class_steady();
            if (!in_array('Core\Interfaces\controller', class_implements($Controller, false))) {
                throw new \Core\Mexception('Controller does not implement interface');
            }
            if (method_exists($Steady, \Config\Controller::STEADY_ACTION)) {
                $action_steady = \Config\Controller::STEADY_ACTION;
            }
            if (method_exists($Controller, $uri['action'])) {
                $action = $uri['action'];
                // Call Controller & Steady
                $Steady->_before($uri);
                $Controller->_before($uri);
                $Steady->$action_steady($uri);
                $Controller->$action($uri);
                $Controller->_after($uri);
                $Steady->_after($uri);
            } else {
                $Steady->_before($uri);
                $Steady->$action_steady($uri);
                $Controller->_fallback($uri);
                $Steady->_after($uri);
            }
            return true;
        }

        /**
         * Closes the class
         */
        public function __desctruct() {

        }

    }

?>
