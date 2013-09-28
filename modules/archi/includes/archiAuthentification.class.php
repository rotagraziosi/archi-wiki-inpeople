<?php
/**
 * Classe ArchiAuthentification
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
/**
 * Gère les sessions des utilisateurs connectés
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
class ArchiAuthentification extends config
{
    private $_login;
    private $_motDePasse;

    //Durée de vie du cookie (en jours)
    static private $_cookie_lifetime = 30;
    
    /**
     * Constructeur de archiAuthentification
     * 
     * @return void
     * */
    function __construct()
    {
        parent::__construct();
        $this->login = "";
        $this->motDePasse = "";
    }


    /**
     * Gère l'ajout d'un utilisateur
     * 
     * @return void
     * */
    public function inscription()
    {
        // analyse du formulaire
        $erreur     = array();
        $securimage = new Securimage();
        $mail       = new mailObject();
        if (trim($this->variablesPost['nom'])=='') {
            $erreur[] = "nom";
        }
        if (trim($this->variablesPost['prenom'])=='') {
            $erreur[] = "prenom";
        }
        if (trim($this->variablesPost['mail'])=='') {
            $erreur[] = "mail";
        } else {
            if (pia_strpos($this->variablesPost['mail'], '@')===false) {
                $erreur[] = "mailinvalide";
            }

            // on verifie que le mail n'existe pas encore dans la base de donnees
            $requeteVerif = "select * from utilisateur where mail='".trim($this->variablesPost['mail'])."'";
            $res = $this->connexionBdd->requete($requeteVerif);
            if (mysql_num_rows($res)>0) {
                $erreur[] = "dejaInscrit";
            }
        }
        if (trim($this->variablesPost['mdp1'])=='') {
            $erreur[] = "mdp1";
        }
        if (trim($this->variablesPost['mdp2'])=='') {
            $erreur[] = "mdp2";
        }
        if (trim($this->variablesPost['mdp1'])!='' && trim($this->variablesPost['mdp2'])!='' && $this->variablesPost['mdp1']!=$this->variablesPost['mdp2']) {
            $erreur[] = "mdpDifferents";
        }
        if ($securimage->check($this->variablesPost['captcha_code'])==false) {
            // the code was incorrect
            // handle the error accordingly with your other error checking
            $erreur[] = "captcha";
        }
        if (count($erreur)>0) {
            echo $this->afficheFormulaireInscription($erreur);
        } else {
            $requeteInscription = "insert into utilisateur (nom,prenom,mail,motDePasse,idVilleFavoris,dateCreation,idProfil) values (
                        '".mysql_escape_string(trim($this->variablesPost['nom']))."',
                        '".mysql_escape_string(trim($this->variablesPost['prenom']))."',
                        '".mysql_escape_string(trim($this->variablesPost['mail']))."',
                        '".mysql_escape_string(md5($this->variablesPost['mdp1']))."',
                        '1',
                        now(),
                        '2'
                        )
                        ";
            $res                = $this->connexionBdd->requete($requeteInscription);
            $newIdUtilisateur   = mysql_insert_id();

            //echo "Vous êtes maintenant inscrit";
            // ***************************************************
            // envoi d'un mail a l'administrateur : un nouvel utilisateur s'est inscrit
            $mailMessageAdminDebut  = _("Un nouvel utilisateur s'est inscrit sur archi-strasbourg :")." <br>";
            $mailMessageAdminDebut .= "<br>";
            $mailMessageAdmin       = _("Nom :")." ".htmlspecialchars($this->variablesPost['nom'])."<br>";
            $mailMessageAdmin      .= _("Prénom :")." ".htmlspecialchars($this->variablesPost['prenom'])."<br>";
            $mailMessageAdmin      .= _("Mail :")." ".htmlspecialchars($this->variablesPost['mail'])."<br>";
            $mail->sendMailToAdministrators($mail->getSiteMail(), _("Un nouvel utilisateur s'est inscrit"), $mailMessageAdminDebut.$mailMessageAdmin, '', true);
            $u = new archiUtilisateur();
            $u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$mailMessageAdmin, 'idTypeMailRegroupement'=>1));

            // ***************************************************
            // envoi d'un mail a l'utilisateur
            $mailMessageUtilisateur  = _("Votre inscription sur archi-strasbourg a été pris en compte.")."<br>";
            $mailMessageUtilisateur .= _("Bonjour").",<br><br>"._("Votre compte n'est pas encore actif.")."<br>";
            $mailMessageUtilisateur .= "<a href='".$this->getUrlRacine().
            "index.php?archiAction=confirmInscription&amp;archiIdUtilisateur=".
            $newIdUtilisateur."&amp;archiMd5=".md5($this->variablesPost['mdp1']).
            "'>"._("Cliquez sur ce lien pour l'activer")."</a>";
            $mail->sendMail(
                $mail->getSiteMail(), $this->variablesPost['mail'],
                _("Votre demande d'inscription sur archi-strasbourg"), $mailMessageUtilisateur, true
            );
            echo _("Inscription prise en compte, vous allez recevoir un mail de confirmation.");

            //echo $this->afficheFormulaireAuthentification();
        }
    }

    /**
     * Connecte un utilisateur et ajoute les infos a la session
     * 
     * @param string $login      Identifiant
     * @param string $motDePasse Mot de passe
     * @param string $cookie     "on" si le cookie doit durer plus que la sessions
     * @param bool   $browserID  Connecté via BrowserID ?
     * 
     * @return void
     * */
    public function connexion($login = "", $motDePasse = "", $cookie = false, $browserID = false)
    {
        if ($cookie=="on") {
            $persistentID=uniqid($login, true);
            setcookie("auth", $persistentID, time() + 86400*self::$_cookie_lifetime);
            $persistentID=substr($persistentID, -23);
            self::addPersistent($login, $persistentID);
        }

        session_regenerate_id();
        
        $requete = '';
        if ($browserID) {
            $res = $this->connexionBdd->requete(
                "SELECT u.idUtilisateur idUtilisateur, u.idVilleFavoris as idVilleFavoris,v.idPays as idPaysFavoris,u.compteActif as compteActif,alerteMail,alerteCommentaires,alerteAdresses,u.idProfil as idProfil
                FROM utilisateur u
                LEFT JOIN ville v ON v.idVille = u.idVilleFavoris
                WHERE mail='".mysql_escape_string($login)."' "
            );
        } else {
            $res = $this->connexionBdd->requete(
                "SELECT u.idUtilisateur idUtilisateur, u.idVilleFavoris as idVilleFavoris,v.idPays as idPaysFavoris,u.compteActif as compteActif,alerteMail,alerteCommentaires,alerteAdresses,u.idProfil as idProfil
                FROM utilisateur u
                LEFT JOIN ville v ON v.idVille = u.idVilleFavoris
                WHERE mail='".mysql_escape_string($login)."' 
                AND motDePasse='".mysql_escape_string(md5($motDePasse))."'"
            );
        }
        if (mysql_num_rows($res)==1) {
            $fetch = mysql_fetch_object($res);
            if ($fetch->compteActif=='1') {
                // on connecte la personne en l'enregistrant dans la session
                $this->session->addToSession('utilisateurConnecte'.$this->idSite, $fetch->idUtilisateur);
                $this->session->addToSession('idVilleFavoris', $fetch->idVilleFavoris);
                $this->session->addToSession('idPaysFavoris', $fetch->idPaysFavoris);
                $this->session->addToSession('utilisateurAlerteCommentaires', $fetch->alerteCommentaires);
                $this->session->addToSession('utilisateurAlerteAdresses', $fetch->alerteAdresses);

                // on ajoute l'utilisateur et la date a la table de la liste des connexions
                $this->connexionBdd->requete("insert into connexionsUtilisateurs (idUtilisateur,date) values ('".$fetch->idUtilisateur."',now()) ");
            } else {
                $this->session->deleteFromSession('utilisateurConnecte'.$this->idSite);
                $this->session->deleteFromSession('idVilleFavoris');
                $this->session->deleteFromSession('idPaysFavoris');
                $this->session->deleteFromSession('utilisateurAlerteCommentaires');
                $this->session->deleteFromSession('utilisateurAlerteAdresses');
                $this->erreurs->ajouter('Connexion : ce compte n\'est pas actif');
            }
        } else {
            $this->session->deleteFromSession('utilisateurConnecte'.$this->idSite);
            $this->session->deleteFromSession('idVilleFavoris');
            $this->session->deleteFromSession('idPaysFavoris');
            $this->session->deleteFromSession('utilisateurAlerteCommentaires');
            $this->session->deleteFromSession('utilisateurAlerteAdresses');
            if ($browserID) {
                $this->erreurs->ajouter(_("Ce compte n'existe pas !")."<br/>"._("Vous pouvez vous inscrire ci-dessous."));
                $this->erreurs->ajouter($this->afficheFormulaireInscription());
            } else {
                $this->erreurs->ajouter(_("Connexion :")." "._("erreur dans vos identifiants"));
            }
        }
    }

    /**
     * Connecte l'utilisateur via BrowserID
     * 
     * @param string $assertion Has renvoyé par https://browserid.org/include.js
     * 
     * @return void
     * */
    public function browserID($assertion)
    {
        $url = "https://browserid.org/verify";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "assertion=".strval($_GET["assertion"])."&audience=".$_SERVER["SERVER_NAME"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(strval(curl_exec($curl)));
        curl_close($curl);
        if ($response->status==="okay") {
            $this->connexion($response->email, null, false, true);
        } else {
            $this->erreurs->ajouter(_("Connexion :")." "._("BrowserID a renvoyé une erreur"));
        }
    }

    /**
     * Deconnecte l'utilisateur en effacant les données de la session
     * 
     * @return void
     * */
    public function deconnexion()
    {
        if (isset($_SESSION)) {
            $this->session->deleteFromSession('utilisateurConnecte'.$this->idSite);
            $this->session->deleteFromSession('utilisateurAdmin');
            $this->session->deleteFromSession('idVilleFavoris');
            $this->session->deleteFromSession('idPaysFavoris');
            $this->session->deleteFromSession('utilisateurAlerteCommentaires');
            $this->session->deleteFromSession('utilisateurAlerteAdresses');

            //Plus simple
            session_destroy();
            session_write_close();
        }
        if (isset($_COOKIE["auth"])) {
            $login = substr($_COOKIE["auth"], 0, -23);
            $id = substr($_COOKIE["auth"], -23);
            $res = $this->connexionBdd->requete(
                "DELETE
                FROM login
                WHERE user='".mysql_escape_string($login)."' 
                AND id='".mysql_escape_string($id)."'
                LIMIT 1"
            );
        }
    }


    /**
     * Est ce que l'utilisateur est admin ?
     * 
     * @param array $params Paramètres
     * Si pas de parametre, on verifie l'utilisateur connecté courant
     * 
     * @return bool
     * */

    public function estAdmin($params = array())
    {
        $retour = false;
        $utilisateur = new archiUtilisateur();
        if ((isset($params['idUtilisateur']) && $params['idUtilisateur']!='') || $this->estConnecte()) {
            if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='') {
                $infos = $utilisateur->getArrayInfosFromUtilisateur($params['idUtilisateur']);
            } else {
                $infos = $utilisateur->getArrayInfosFromUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
            }
            $idProfil    = $infos['idProfil'];
            $d           = new droitsObject();
            $arrayProfil = $d->getProfilFromIdProfil($idProfil);
            if ($arrayProfil['libelle']=='administrateur') {
                $retour = true;
            }
        }
        return $retour;
    }

    /**
     * Est ce que l'utilisateur est moderateur ?
     * ATTENTION : cette fonction ne précise pas si l'utilisateur est moderateur pour une ville,
     * donc il faudra encore faire la verif de la ville
     * 
     * @param array $params Paramètres
     * Si pas de parametre, on verifie l'utilisateur connecté courant
     * 
     * @return bool
     * */
    public function estModerateur($params = array())
    {
        $retour = false;
        $utilisateur = new archiUtilisateur();
        if ((isset($params['idUtilisateur']) && $params['idUtilisateur']!='') || $this->estConnecte()) {
            if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='' && $utilisateur->getIdProfil($params['idUtilisateur'])=='3') {
                $retour = true;
            } elseif ($utilisateur->getIdProfil($this->session->getFromSession('utilisateurConnecte'.$this->idSite))=='3') {
                $retour = true;
            }
        }
        return $retour;
    }


    /**
     * Est ce que l'utilisateur veut recevoir les alertes sur les commentaires
     * 
     * @return bool
     * */
    public function isAlerteCommentaires()
    {
        $retour = false;
        if ($this->session->isInSession('utilisateurAlerteCommentaires') AND $this->session->getFromSession('utilisateurAlerteCommentaires')==1) {
            $retour = true;
        }
        return $retour;
    }


    /**
     * Est ce que l'utilisateur veut recevoir les alertes sur les adresses ?
     * 
     * @return bool
     * */
    public function isAlerteAdresses()
    {
        $retour = false;
        if ($this->session->isInSession('utilisateurAlerteAdresses') AND $this->session->getFromSession('utilisateurAlerteAdresses')==1) {
            $retour = true;
        }
        return $retour;
    }

    /**
     * Est ce que l'utilisateur est connecté ?
     * 
     * @return bool
     * */
    public function estConnecte()
    {
        if ($this->session->isInSession('utilisateurConnecte'.$this->idSite)) {
            return true;
        } else if (self::checkPersistent()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Renvoi l'id de l'utilisateur connecté
     * 
     * @return int ID
     * */
    public function getIdUtilisateur()
    {
        $retour = 0;
        if ($this->session->isInSession('utilisateurConnecte'.$this->idSite)) {
            $retour = intval($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
        }
        return $retour;
    }


    /** 
     * Renvoi l'idProfil de l'utilisateur connecté
     * 
     * @return int ID
     * */
    public function getIdProfil()
    {
        $idUtilisateurConnecte = 0;
        if ($this->session->isInSession('utilisateurConnecte'.$this->idSite)) {
            $idUtilisateurConnecte = $this->session->getFromSession('utilisateurConnecte'.$this->idSite);
        }
        $u = new archiUtilisateur();
        return intval($u->getIdProfilFromUtilisateur($idUtilisateurConnecte));
    }

    /** Affichage du formulaire d'authentification
     * 
     * @param string $modeAffichage Mode d'affichage (compact ou noCompact)
     * @param array  $params        Paramètres supplémentaires
     * 
     * @return string HTML 
     * */
    public function afficheFormulaireAuthentification($modeAffichage = 'noCompact', $params = array())
    {
        $t                                         = new Template('modules/archi/templates/');
        $t->set_filenames((array('authentification'=>'authentification.tpl')));
        $authentification                          = new archiAuthentification();
        $t->assign_block_vars($modeAffichage, array());
        if (!isset($params['msg'])) {
            $t->assign_vars(array('msg'=>"<b>"._("Afin de pouvoir ajouter une adresse ou en modifier une existante vous devez, au préalable, créer votre compte personnel sur www.archi-strasbourg.org. Pour cela inscrivez-vous en cliquant")." <a href='".$this->creerUrl('', 'inscription')."'>"._("ici").".</a></b>"));
        } else {
            $t->assign_vars(array('msg'=>$params['msg']));
        }
        $actionAAppeler = "";

        // action que l'authentification va permettre d'atteindre par la validation du formulaire si l'authentification est ok
        if (isset($this->variablesGet['archiAction'])) {
            $actionAAppeler = $this->variablesGet['archiAction'];
        } elseif (isset($this->variablesGet['archiAffichage'])) {
            $actionAAppeler = $this->variablesGet['archiAffichage'];
        }
        $t->assign_vars(array('ACTIONFORM'=>$this->creerUrl('validAuthentification', '', array('archiActionPrecedente'=>$actionAAppeler), true)));
        ob_start();
        $t->pparse('authentification');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Affichage du formulaire d'inscription
     * 
     * @param array $erreur Erreur à afficher
     * 
     * @return string HTML
     * */
    public function afficheFormulaireInscription($erreur = array())
    {
        $t                                    = new Template('modules/archi/templates/');
        $t->set_filenames((array('inscription'=>'inscription.tpl')));
        $t->assign_vars(array('ACTIONFORM'    =>$this->creerUrl("validInscription")));
        if (isset($this->variablesPost['nom'])) {
            $t->assign_vars(array('nom'=>htmlspecialchars($this->variablesPost['nom'], ENT_QUOTES)));
        }
        if (isset($this->variablesPost['prenom'])) {
            $t->assign_vars(array('prenom'=>htmlspecialchars($this->variablesPost['prenom'], ENT_QUOTES)));
        }
        if (isset($this->variablesPost['mail'])) {
            $t->assign_vars(array('mail'=>htmlspecialchars($this->variablesPost['mail'], ENT_QUOTES)));
        }
        if (count($erreur)>0) {
            foreach ($erreur as $indice=>$intituleErreur) {
                switch ($intituleErreur) {
                case 'nom':
                    $t->assign_vars(array('nomErreur'=>_("Vous n'avez pas renseigné le champ nom")));
                    break;

                case 'prenom':
                    $t->assign_vars(array('prenomErreur'=>_("Vous n'avez pas renseigné le champ prenom")));
                    break;

                case 'mail':
                    $t->assign_vars(array('mailErreur'=>_("Vous n'avez pas renseigné le champ mail")));
                    break;

                case 'mdp1':
                    $t->assign_vars(array('mdp1Erreur'=>_("Vous n'avez pas renseigné le champ mot de passe")));
                    break;

                case 'mdp2':
                    $t->assign_vars(array('mdp2Erreur'=>_("Vous n'avez pas renseigné le champ mot de passe")));
                    break;

                case 'mdpDifferents':
                    $t->assign_vars(array('mdpDifferentsErreur'=>_("Les deux mots de passe diffèrent")));
                    break;

                case 'mailinvalide':
                    $t->assign_vars(array('mailErreur'=>_("Le mail est invalide")));
                    break;

                case 'dejaInscrit':
                    $t->assign_vars(array('mailErreur'=>_("Ce mail est déjà enregistré")));
                    break;

                case 'captcha':
                    $t->assign_vars(array('captchaErreur'=>_("Le code ne correspond pas")));
                    break;
                }
            }
        }
        ob_start();
        $t->pparse('inscription');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Fonction obsolete pour rendre un utilisateur admin
     * 
     * @param int $idUtilisateur ID de l'utilisateur
     * 
     * @return string
     * */
    public function rendreAdmin($idUtilisateur)
    {
        $html = '';
        if ($this->estAdmin()==1) {
            if (is_numeric($idUtilisateur) AND $idUtilisateur>0) {
                $this->connexionBdd->requete('UPDATE utilisateur SET idProfil=4 WHERE idutilisateur='.$idUtilisateur.' LIMIT 1');
                $html .= _("Vous venez de modifier le statut de")." ".mysql_affected_rows()." "._("utilisateur");
            } else {
                $html .= _("Erreur, vous n'avez pas donné un id valide !");
                $this->erreurs->ajouter(_("Rendre Admin :")." "._("Erreur, vous n'avez pas donné un id valide !"));
            }
        } else {
            $html .= 'Erreur, vous n\'êtes pas administrateur !';
            $this->erreurs->ajouter(_("Rendre Admin :")." "._("Erreur, vous n'êtes pas administrateur !"));
        }
        return $html;
    }
    
    /**
     * Vérifie si un cookie de connexion persistente est valide
     * 
     * @return bool
     * */
    static function checkPersistent ()
    {
        global $config;
        if (isset($_COOKIE["auth"])) {
            $login = substr($_COOKIE["auth"], 0, -23);
            $id = substr($_COOKIE["auth"], -23);
            $res = $config->connexionBdd->requete(
                "SELECT id
                FROM login
                WHERE user='".mysql_real_escape_string($login)."' 
                AND id='".mysql_real_escape_string($id)."'"
            );
            $fetch = mysql_fetch_object($res);
            if (isset($fetch->id) && $fetch->id==$id) {
                $resUser = $config->connexionBdd->requete(
                    "SELECT u.idUtilisateur idUtilisateur, u.idVilleFavoris as idVilleFavoris,v.idPays as idPaysFavoris, alerteCommentaires,alerteAdresses
                    FROM utilisateur u
                    LEFT JOIN ville v ON v.idVille = u.idVilleFavoris
                    WHERE mail='".mysql_escape_string($login)."' "
                );
                $fetch = mysql_fetch_object($resUser);
                $config->session->addToSession('utilisateurConnecte'.$config->idSite, $fetch->idUtilisateur);
                $config->session->addToSession('idVilleFavoris', $fetch->idVilleFavoris);
                $config->session->addToSession('idPaysFavoris', $fetch->idPaysFavoris);
                $config->session->addToSession('utilisateurAlerteCommentaires', $fetch->alerteCommentaires);
                $config->session->addToSession('utilisateurAlerteAdresses', $fetch->alerteAdresses);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Ajouter une connexion persistente
     * 
     * @param string $login Adresse e-mail de l'utilisateur
     * @param string $id    Identifiant unique
     * 
     * @return void
     * */
    static function addPersistent ($login, $id)
    {
        global $config;
        $res = $config->connexionBdd->requete(
            "INSERT INTO `login` (
                `id` ,
                `user`
            )
            VALUES (
                '$id', '$login'
            );"
        );
    }
}
?>
