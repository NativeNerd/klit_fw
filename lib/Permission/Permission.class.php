<?php
    namespace Lib;
    /**
     * [Permission.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Permission implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        protected $Path;
        protected $table;
        protected $permission = 0x00000000;
        protected $key = 0x0000000;

        public function __construct() {
            $this->Path = \Lib\Path::getInstance();
            return ;
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

        public function addGroup($groupName) {
            $this->table[$groupName] = array();
        }

        public function addPermission($groupName, $categoryName, $permission, $value) {
            $this->table[$groupName][$categoryName][$permission] = ($value) ? 1 : 0;
        }

        public function setPermission() {

        }

        public function hasPermission() {

        }

        protected function keyUp() {
            $key = hexdec($this->key);
            $key += 1;
            $this->key = dechex($key);
            return true;
        }

        public function import($groupName) {
            if (($path = $this->Path->buildPath(\Config\Permission::DIR . $groupName)) !== false) {
                $xml = new \SimpleXMLElement($path, null, true);
                foreach ($xml->group AS $group) {
                    $groupName = (string) $group['name'];
                    $this->addGroup($groupName);
                    foreach ($group->list->category AS $cat) {
                        $catName = (string) $cat['name'];
                        foreach ($cat->item AS $item) {
                            $itemName = (string) $item['name'];
                            $itemValue = (int) $item['value'];
                            $this->addPermission($groupName,
                                $catName,
                                $itemName,
                                $itemValue);
                        }
                    }
                }
            }
        }

        public function export() {
            $csv = array();
            foreach ($this->table AS $groupName=>$group) {
                foreach ($group AS $catName=>$cat) {
                    foreach ($cat AS $itemName=>$item) {
                        $this->keyUp();
                        $csv[] = implode(';',
                            array($this->key,
                                $groupName,
                                $catName,
                                $itemName,
                                $item."\n")
                            );
                    }
                }
            }
            var_dump(file_put_contents($this->Path->buildPath(\Config\Permission::DIR.'root.import', false), $csv));
        }

        public function __destruct() {

        }

    }

?>
