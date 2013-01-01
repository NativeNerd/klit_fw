<?php
    namespace Lib\Permission;
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

        protected $lawTable;
        protected $ruleTable;
        protected $permissionTable;

        protected $activeGroups;

        public function __construct() {
            $this->Path = \Lib\Path\Path::getInstance();
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

        public function assignGroup($groupName) {
            if (isset($this->lawTable[$groupName])) {
                $this->activeGroups[$this->lawTable[$groupName]] = $groupName;
                return true;
            }
            return false;
        }

        public function setGroup($groupName, $level) {
            $this->lawTable[$groupName] = (int) $level;
        }

        public function setRule($groupName, $catName, $itemName, $value) {
            $this->ruleTable[$groupName][$catName][$itemName] = ($value) ? true : false;
        }

        public function setPermission($groupName, $catName, $itemName, $value) {
            $this->permissionTable[$groupName][$catName][$itemName] = ($value) ? true : false;
        }

        public function has($catName, $itemName = null) {
            if (strstr($catName, ':')) {
                $explode = explode(':', $catName, 2);
                $catName = $explode[0];
                $itemName = $explode[1];
            }
            /**
             * Vorgehensweise:
             * 1. Prüfe auf Rule mit to=*
             * 2. Prüfe auf Rule mit to=$groupName (level aufsteigend)
             * 3. Prüfe auf Permission mit to=$groupName (level aufsteigend)
             */
            if (isset($this->ruleTable['*'][$catName][$itemName])) {
                return $this->ruleTable['*'][$catName][$itemName];
            }
            foreach ($this->activeGroups AS $groupName) {
                if (isset($this->ruleTable[$groupName][$catName][$itemName])) {
                    return $this->ruleTable[$groupName][$catName][$itemName];
                }
            }
            foreach ($this->activeGroups AS $groupName) {
                if (isset($this->permissionTable[$groupName][$catName][$itemName])) {
                    return $this->permissionTable[$groupName][$catName][$itemName];
                }
            }
            return false;
        }

        /**
         * Imports a *.law-File, creates automatically all IDs if needed and writes down the file
         *
         * @param file $law
         * @return boolean
         * @throws \Core\Mexception
         */
        public function useLaw($law) {
            if (($path = $this->Path->buildPath(\Config\Permission::DIR . $law)) !== false) {
                $law = new \SimpleXMLElement($path, null, true);
                foreach ($law->groups AS $groups) {
                    $groupName = (string) $groups->group['name'];
                    $groupLevel = (int) $groups->group['level'];
                    $this->setGroup($groupName, $groupLevel);
                }
                foreach ($law->rules AS $rules) {
                    $rulesTo = (string) $rules['to'];
                    foreach ($rules->category AS $cat) {
                        $catName = (string) $cat['name'];
                        foreach ($cat->item AS $item) {
                            $itemName = (string) $item['name'];
                            $itemValue = (int) $item['value'];
                            $this->setRule($rulesTo, $catName, $itemName, $itemValue);
                        }
                    }
                }
                return true;
            }
        }

        /**
         * Imports a *.group-File
         * @param type $groupName
         */
        public function useGroup($groupName) {
            if (($path = $this->Path->buildPath(\Config\Permission::DIR . $groupName)) !== false) {
                $xml = new \SimpleXMLElement($path, null, true);
                foreach ($xml->group AS $group) {
                    $groupName = (string) $group['name'];
                    foreach ($group->category AS $cat) {
                        $catName = (string) $cat['name'];
                        foreach ($cat->item AS $item) {
                            $itemName = (string) $item['name'];
                            $itemValue = (int) $item['value'];
                            $this->setPermission($groupName, $catName, $itemName, $itemValue);
                        }
                    }
                }
            }
        }

        public function __destruct() {

        }

    }

?>
