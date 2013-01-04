<?php
    namespace Lib\Session;
    /**
     * [Session.class.php]
     * @name Session.class.php
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Handles Sessions
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Session implements \Core\Interfaces\Lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        protected $Id;
        protected $savePath;

        /**
         * Initializes a Session
         * @param Model $Model
         */
        public function __construct() {
            session_start();
            $this->Id = session_id();
        }

        public static function getInstance(\Core\Bootstrap $Bootstrap = null) {
            if ($Bootstrap !== null) {
                static::$Bootstrap = $Bootstrap;
            }
            if (static::$_instance === null) {
                static::$_instance = new static();
            }
            return static::$_instance;
        }

        /**
         * Sets a save path for the session files
         *
         * @param string $path
         * @return boolean
         */
        public function setSavePath($path) {
            if (Helper::buildPath($path)) {
                $this->savePath = $path;
                session_save_path($path);
                return true;
            } else {
                return false;
            }
        }

        /**
         * Return the actual Session ID
         * @return type
         */
        public function getId() {
            return $this->Id;
        }

        /**
         * Updates the Session ID
         */
        public function updateId() {
            $this->Id = session_id();
        }

        /**
         * Sets a value on $_SESSION
         * You can use as easy-write-style foo->bar as foo[bar]
         *
         * @param mixed $name
         * @param mixed $value
         * @return boolean
         */
        public function setValue($name, $value) {
            if (strpos($name, '->')) {
                $name = explode('->', $name, 2);
                $_SESSION[$name[0]][$name[1]] = serialize($value);
            } else {
                $_SESSION[$name] = serialize($value);
            }
            return true;
        }

        /**
         * Gets a value on $_SESSION
         * You can use as easy-write-style foo-bar as foo[bar]
         *
         * @param mixed $name
         * @return mixed
         */
        public function getValue($name) {
            if (strpos($name, '->')) {
                $name = explode('->', $name, 2);
                if (isset($_SESSION[$name[0]][$name[1]])) {
                    return unserialize($_SESSION[$name[0]][$name[1]]);
                } else return null;
            } else {
                if (isset($_SESSION[$name])) {
                    if (is_array($_SESSION[$name])) {
                        return $_SESSION[$name];
                    } else {
                        return unserialize($_SESSION[$name]);
                    }
                } else return null;
            }
        }

        /**
         * Deletes a value on $_SESSION
         *
         * @param mixed $name
         */
        public function deleteValue($name) {
            if (strpos($name, '->')) {
                $name = explode('->', $name, 2);
                unset($_SESSION[$name[0]][$name[1]]);
            } else unset($_SESSION[$name]);
        }

        /**
         * Regenerates the Session ID
         * Also calles updateId() automatically
         *
         * @return boolean
         * @throws Mexception
         */
        public function regenerate() {
            if (session_regenerate_id(true)) {
                $this->updateId();
                return true;
            }
            else throw new Mexception('Unable to regenerate Session');
        }

        /**
         * Closes the session and deletes all values
         *
         * @return boolean
         */
        public function closeSession() {
            session_unset();
            return true;
        }

        /**
         * Writes the session down and closes the object
         *
         * @return boolean
         */
        public function __destruct() {
            session_write_close();
            return true;
        }
    }
?>
