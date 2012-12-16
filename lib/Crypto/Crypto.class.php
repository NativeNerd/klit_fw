<?php
    namespace Lib;
    /**
     * [Crypto.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     * @desc De- and encrypts a given string with mcrypt
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     *
     */
    class Crypto implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        protected $keyDir = 'keys';
        protected $cipher;
        protected $mode;
        protected $iv = null;
        protected $key = null;

        /**
         * Opens a new Crypto
         *
         * @param MCRYPT-Cipher $cipher
         * @param MCRYPT-Mode $mode
         * @throws Mexception
         * @return boolean
         */
        public function __construct($cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CFB) {
            if (extension_loaded('mcrypt')) {
                throw new Mexception('Mcrypt not installed');
            }
            if (in_array($cipher, mcrypt_list_algorithms())) {
                $this->cipher = $cipher;
            } else throw new Mexception('Cipher is not allowed.');
            if (in_array($mode, mcrypt_list_modes())) {
                $this->mode = $mode;
            } else throw new Mexception('Mode not allowed');
            if (!class_exists('Helper'))
                throw new Mexception('Class Helper not found');
            $this->keyDir = Helper::buildPath($this->keyDir);
            return true;
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
         * Sets an IV
         *
         * @param string $iv
         * @return boolean
         * @throws Mexception
         */
        public function setIv($iv) {
            if (strlen($iv) != mcrypt_get_iv_size($this->cipher, $this->mode))
                throw new Mexception('IV string not valid');
            else {
                $this->iv = $iv;
                return true;
            }
        }

        /**
         * Creates and returns an iv
         *
         * If $setToThis is true, $this->setIv will be called automatically
         * @param boolean $setToThis
         * @return type
         */
        public function createIv($setToThis = true) {
            $size = mcrypt_get_iv_size($this->cipher, $this->mode);
            $iv = mcrypt_create_iv($size);
            if ($setToThis)
                $this->setIv($iv);
            return $iv;
        }

        /**
         * Creates a key
         *
         * @return string
         */
        public function createKey() {
            $token = array();
            $token[] = microtime();
            $token[] = $_SERVER['REMOTE_ADDR'];
            $token[] = rand(1000, 9999);
            $token[] = $this->createIv(false);

            $finalToken = '';
            foreach($token AS $value) {
                $finalToken .= substr(sha1($value), -10, 5);
            }
            $finalToken .= $this->createIv(false);

            return $finalToken;
        }

        /**
         * Sets a key
         *
         * @param string $key
         * @return boolean
         */
        public function setKey($key) {
            $this->key = $key;
            return true;
        }

        /**
         * Sets a key which is stored in a file
         *
         * @param string $filename
         * @throws Mexception
         */
        public function setKeyByFile($id) {
            if (!file_exists($this->keyDir.$filename))
                throw new Mexception('Key does not exist any more');
            $this->key = file_get_contents($this->keyDir.$filename);
        }

        /**
         * Encrypts a readable text
         *
         * @param string $readableText
         * @return string
         * @throws Mexception
         */
        public function encrypt($readableText) {
            if ($this->iv !== null AND $this->key !== null) {
                return mcrypt_encrypt($this->cipher, $this->key, $readableText, $this->mode, $this->iv);
            } else
                throw new Mexception('Not allowed');
        }

        /**
         * Decrypts a readable text
         * Iv and key has to be set to allow this function
         *
         * @param string $encryptedText
         * @return string
         * @throws Mexception
         */
        public function decrypt($encryptedText) {
            if ($this->iv AND $this->key) {
                return mcrypt_decrypt($this->cipher, $this->key, $encryptedText, $this->mode, $this->iv);
            } else
                throw new Mexception('Not allowed');
        }

        public function __destruct() {

        }
    }
?>
