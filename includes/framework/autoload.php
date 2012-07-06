<?php
/**
 * Autoload class
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiStrasbourg
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
 /**
 * Charge uniquement les classes nécessaires
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiStrasbourg
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
class Autoload
{
    /**
     * Essaie de charger une classe
     * 
     * @param string $class_name Nom de la classe
     * 
     * @return void
     * */
    static function load($class_name)
    {
        if (stream_resolve_include_path(
            "modules/archi/includes/".$class_name.".class.php"
        )) {
            include_once "modules/archi/includes/".$class_name.".class.php";
        } else if (stream_resolve_include_path(
            __DIR__."/frameworkClasses/".$class_name.".class.php"
        )) {
            include_once __DIR__."/frameworkClasses/".$class_name.".class.php";
        }

    }
}
spl_autoload_register("Autoload::load");
?>
