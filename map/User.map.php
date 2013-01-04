<?php
    namespace Map;
    /**
     * [User.map.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class User extends Core\Interfaces\map {
        protected $user_id;
        protected $user_lastlogin;
        protected $user_name;
        protected $user_password;
        protected $user_mail;

        public function setId($id) { $this->user_id = $id; }
        public function setName($name) { $this->user_name = $name; }
        public function setMail($mail) { $this->user_mail = $mail; }
    }
?>
