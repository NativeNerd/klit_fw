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
     *
     */
    final class Helper {
        final static public function buildPath($pathInProjectDir, $path = null, $extension = null, $error = true) {
            if (substr($pathInProjectDir, 0, 2) == '..') {
                $pathInProjectDir = substr($pathInProjectDir, 3);
            }
            $dir = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen(basename($_SERVER['SCRIPT_FILENAME'])))
                .$path
                .$pathInProjectDir;
            if (is_dir($dir))
                $dir .= '/';
            elseif (is_file($dir))
                $dir .= $extension;
            elseif ($error)
                return false;
            return $dir;
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

        final static public function form_byType($type, $value) {
            switch ($type) {
                case 'string' :
                    return Helper::form_string($value);
                case 'mail' :
                    return Helper::form_mail($value);
                case 'int' :
                    return Helper::form_int($value);
                default :
                    return null;
            }
        }

        final static public function form_string($string) {
            if (is_string($string)) {
                return true;
            }
        }

        final static public function form_mail($mail) {
            if (filter_var($mail, FILTER_VALIDATE_EMAIL) == false) {
                return false;
            } else {
                return true;
            }
        }

        final static public function form_int($value) {
            if (is_numeric($value)) {
                return true;
            } else {
                return false;
            }
        }
    }
?>