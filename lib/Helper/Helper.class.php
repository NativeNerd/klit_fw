<?php
    /**
     * [Helper.class.php]
     * @version 1.1.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     * 1.0.0        1.1.0   buildPath() neu ohne $character, gibt nun Fehler bei inexistentem Pfad (als default)
     *              1.1.0   buildPath() now without $path (now $_unused)
     *
     */
    namespace Lib;
    final class Helper {
        final static public function buildPath($path, $_unused = null, $extension = null, $error = true) {
            if (substr($path, 0, 2) == '..') {
                $path = substr($path, 3);
                return \Lib\Helper::buildPath($path, null, $extension, $error);
            }

            // Project directory
            $dir = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen(basename($_SERVER['SCRIPT_FILENAME'])))
                .$path;

            if (is_dir($dir))
                return $dir .= '/';
            elseif (is_file($dir))
                return $dir;
            elseif ($error)
                throw new \Core\Mexception('Invalid path given');
            else
                return false;
        }

        final static public function buildDate($timestamp = null) {
            if ($timestamp == null)
                $timestamp = time();
            return date('Y-m-d', $timestamp);
        }

        final static public function buildDatetime($timestamp = null) {
            if ($timestamp == null)
                $timestamp = time();
            return date('Y-m-d H:i:s', $timestamp);
        }

        final static public function getIp() {
            return $_SERVER['REMOTE_ADDR'];
        }

        final static public function parseUri() {
            if (strlen($_SERVER['QUERY_STRING']) > 0) {
                $str = explode('&', $_SERVER['QUERY_STRING']);
                $newKey = array();
                $newValue = array();
                foreach ($str AS $value) {
                    if (substr($value, 0, 1) == '/') {
                        $value = substr($value, 1);
                    }
                    $value = explode('=', $value);
                    $newKey[] = $value[0];
                    $newValue[] = $value[1];
                }
                return array_combine($newKey, $newValue);
            } else {
                return false;
            }
        }

        /**
         * @todo Implement to Query
         */
        final static public function db_notationByType($value, $type) {
            $matches = array();
            if (!preg_match('$([a-zA-Z]+)\(([0-9]+)\)$', $type, $matches)) {
                if (!preg_match('$([a-zA-Z]+)$', $type, $matches)) {
                    return null;
                }
            }
            switch (strtolower($matches[1])) {
                case 'varchar' :
                    return '"'.$value.'"';
                case 'int' :
                    return $value;
                case 'float' :
                    return $value;
                case 'bool' :
                    if ($value == 1 OR $value == true) {
                        return 'TRUE';
                    } else {
                        return 'FALSE';
                    }
                case 'decimal' :
                    return $value;
                case 'datetime' :
                    return '"'.$value.'"';
                default :
                    return null;
            }
        }
    }
?>