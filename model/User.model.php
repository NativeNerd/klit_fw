<?php
    namespace Model;
    /**
     * [User.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class User extends \Core\Implement\model {
        protected $Bean;
        protected $Query;

        public function __construct() {
            $this->Bean = new \Map\User;
            $this->Query = new \Lib\Query\Query;
            return ;
        }

        public function getUserById($id) {
            $result = $this->Query->select()
                ->primary($id)
                ->table('user');
            $row = $result->fetch_assoc();
            $this->Bean->user_id = $row['id'];
            return ;
        }

        public function __desctruct() {

        }
    }

?>
