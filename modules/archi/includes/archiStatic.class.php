<?php
/**
 * Classe ArchiStatic
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
 * Cette classe affiche les parties statiques du site comme la FAQ ou l'EDITO
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
 * */
class ArchiStatic extends config
{
    
    /**
     * Constructeyr d'ArchiStatic
     * 
     * @return void
     * */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Affiche une page statique
     * 
     * @return string HTML
     * */
    function afficheFaq()
    {
        $html = '';
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('faq'=>'faq.tpl'));
        
        $t->assign_vars(array('urlEnregistrement'=>$this->creerUrl('', 'inscription')));
        $t->assign_vars(array('urlRecherche'=>$this->creerUrl('', 'recherche', array('submit'=>'Rechercher', 'motcle'=>''))));
        ob_start();
        $t->pparse('faq');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }

    /**
     * Affiche une page statique
     * ajout de cette fonctione le 30/11/2011 (suite rencontre du 26/11/2011 et don pour afficher le logo et un lien vers asso)
     * 
     * @return string HTML
     * */
    function afficheDonateurs()            
    {
        $html = '';
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('donateurs'=>'donateurs.tpl'));
        
        $t->assign_vars(array('urlEnregistrement'=>$this->creerUrl('', 'inscription')));
        $t->assign_vars(array('urlRecherche'=>$this->creerUrl('', 'recherche', array('submit'=>'Rechercher', 'motcle'=>''))));
        ob_start();
        $t->pparse('donateurs');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Affiche une page statique
     * 
     * @return string HTML
     * */
    function afficheEdito()
    {
        $html = '';
        $mail = new mailObject();
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('edito'=>'edito.tpl'));
        $t->assign_vars(array('email'=>$mail->encodeEmail("fabien.romary@gmail.com")));
        
        ob_start();
        $t->pparse('edito');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Affiche une page statique
     * 
     * @return string HTML
     * */
    function affichePresseMediaPublicite()
    {
        $html = '';

        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('publicite'=>'staticArticleDNA.tpl'));
        
        
        if (isset($this->variablesGet['archiAncre']) && $this->variablesGet['archiAncre']==1) {
            $t->assign_vars(
                array('jsToExecute'=>"
            
                <script  >
                    document.getElementById('blocDNA').style.display='block';
                    document.getElementById('blocAfficher').style.display='none';
                    location.href='#dna';
                </script>
                
                ")
            );
        } elseif (isset($this->variablesGet['archiAncre']) && $this->variablesGet['archiAncre']==2) {
            $t->assign_vars(
                array('jsToExecute'=>"
                <script  >
                    location.href='#france3';
                </script>
                ")
            );
        
        }
        
        
        $t->assign_vars(
            array(
                'cheminPhoto'=>$this->getUrlImage()."/publicite/photoArticleDNA.jpg", 
                'htmlPhoto'=>"", 
                'cheminLogoDNA'=>$this->getUrlImage()."/publicite/logoDNA.jpg", 
                'cheminLogoFrance3'=>$this->getUrlImage()."/publicite/logoFrance3Alsace.jpg", 
                'lienPDF'=>$this->getUrlImage()."/publicite/LOFST.11.0051DNAwww.archi-strasbourg.org.pdf", 
                'urlVideo'=>$this->getUrlImage()."/publicite/reportageFrance3.wmv"
            )
        );
        
        ob_start();
        $t->pparse('publicite');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    
    }
    
    /**
     * Affiche une page statique
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheQuiSommesNous($params=array())
    {
        $html = '';

        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('quiSommesNous'=>'staticQuiSommesNous.tpl'));
        
        $t->assign_vars(
            array(
            'lienProfilLaurent'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>30)), 
            'lienProfilFabien'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>31)), 
            'lienProfilBurckel'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>116)), 
            'lienProfilHelmlinger'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>95)), 
            'lienProfilLohner'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>101)), 
            'lienProfilRiviere'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>86))
            )
        );
        
        ob_start();
        $t->pparse('quiSommesNous');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    
    }

    /**
     * Affiche une page statique
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheQuiSommesNousCreationAssociation2011($params=array())
    {
        $html = '';

        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('quiSommesNous'=>'staticQuiSommesNous20110923CreationAssociation.tpl'));
        
        $t->assign_vars(
            array(
            'lienProfilLaurent'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>30)), 
            'lienProfilFabien'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>31)), 
            'lienProfilBurckel'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>116)), 
            'lienProfilHelmlinger'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>95)), 
            'lienProfilLohner'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>101)), 
            'lienProfilRiviere'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>86))
            )
        );
        
        ob_start();
        $t->pparse('quiSommesNous');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }

    /**
     * Affiche une page statique
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheQuiSommesNousContributeurs2010($params=array())
    {
        $html = '';

        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('quiSommesNous'=>'staticQuiSommesNous2010ClubDesSept.tpl'));
        
        $t->assign_vars(
            array(
            'lienProfilLaurent'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>30)), 
            'lienProfilFabien'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>31)), 
            'lienProfilBurckel'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>116)), 
            'lienProfilHelmlinger'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>95)), 
            'lienProfilLohner'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>101)), 
            'lienProfilRiviere'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>86))
            )
        );
        
        ob_start();
        $t->pparse('quiSommesNous');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    
    }
    
    /**
     * Affiche une page statique
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheQuiSommesNousLaurent2009($params=array())
    {
        $html = '';

        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('quiSommesNous'=>'staticQuiSommesNous2009Laurent.tpl'));
        
        $t->assign_vars(
            array(
            'lienProfilLaurent'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>30)), 
            'lienProfilFabien'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>31)), 
            'lienProfilBurckel'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>116)), 
            'lienProfilHelmlinger'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>95)), 
            'lienProfilLohner'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>101)), 
            'lienProfilRiviere'=>$this->creerUrl('', 'detailProfilPublique', array('archiIdUtilisateur'=>86))
            )
        );
        
        ob_start();
        $t->pparse('quiSommesNous');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Affiche une page statique
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheFaireUnDon($params = array())
    {
        $html = "";
    
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('faireUnDon'=>'staticFaireUnDon.tpl'));
        $t->assign_vars(
            array(
                "lang"=>LANG
            )
        );
        
        
        
        
        

        
        ob_start();
        $t->pparse('faireUnDon');
        $html .= ob_get_contents();
        ob_end_clean();
    
        return $html;
    }
    
}
?>
