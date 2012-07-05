<?php
/**
 * Fonctions de debug
 * 
 * PHP Version 5.3.3
 * 
 * @category Debug
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */

/**
 * Fonction appelée quand le programme plante
 * 
 * @return void
 * */
function shutdown()
{
    print_r(error_get_last());
}
if (isset($config->debug) && $config->debug) {
    register_shutdown_function("shutdown");
}
?>
