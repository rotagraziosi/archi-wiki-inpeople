<?php
/**
 * Classe ArchiParcours
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
/**
 * Gère les parcours
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
class ArchiParcours
{
    /**
     * Constructeur de la classe ArchiParcours
     * 
     * @param int $id Identifiant du parcours
     * 
     * @return void
     * */
    function __construct ($id)
    {
        $config = new config();
        $query = 'SELECT commentaireParcours FROM parcoursArt WHERE idParcours="'.
        $config->connexionBdd->quote($id).'";';
        $result = $config->connexionBdd->requete($query);
        $result = mysql_fetch_assoc($result);
        $this->desc = stripslashes($result['commentaireParcours']);
    }
}
