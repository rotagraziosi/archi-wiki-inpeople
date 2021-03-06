<?php
/**
 * Classe MailObject
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
// classe de gestion des mails
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - separation de la classe de mail de l'objet config
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

class MailObject extends config
{
    
    /**
     * Constructeur de mailObject
     * 
     * @param string $connexion Connexion
     * 
     * @return void
     * */
    function __construct($connexion='')
    {
        parent::__construct();    //    
    }

    /**
     * ?
     * 
     * @return ?
     * */
    public function getSiteMail()
    {
        return $this->siteMail;
    }

    /**
     * Verification de la validité d'un mail 
     * 
     * @param string $mail E-mail
     * 
     * @return bool
     * */
    public function isMail($mail="")
    {
        $retour = false;
        $cond = preg_match(
            "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@".
            "([\.a-zA-Z0-9_-])+\.([a-zA-Z0-9])+$/", $mail
        );
        if ($cond) {
            $retour = true;
        }
        
        return $retour;
    }

    /**
     * Envoi d'un mail 
     * 
     * @param string $envoyeur        Emetteur
     * @param string $destinataire    Destinataire
     * @param string $sujet           Sujet
     * @param string $message         Message
     * @param bool   $writeMailToLogs Ajouter le mail aux logs
     * @param string $replyTo         Adresse de réponse
     * @param string $logfile         Fichier de log
     * 
     * @return bool
     * */
    public function sendMail(
        $envoyeur='',$destinataire='',$sujet='',$message='',
        $writeMailToLogs=false, $replyTo=null, $logfile='mail.log'
    ) {
        $headers ='From: "'.$envoyeur.'"<'.$envoyeur.'>'.
            PHP_EOL."Content-Type: text/html; charset=utf-8".PHP_EOL;
        if (!empty($replyTo)) {
            $headers.="Reply-To: $replyTo";
        }
        
        //$sujet = iconv("UTF-8","ISO-8859-15//IGNORE", $sujet);

        //$message  = iconv("UTF-8","ISO-8859-15//IGNORE", $message);
        $retour = false;
        if (isset($this->isSiteLocal) && $this->isSiteLocal==true) {
            echo "Envoi d'un mail à $destinataire from $envoyeur<br>";
            echo $headers;
            echo "subject : $sujet<br>";
            echo "$message<br>";
            echo "finMail<br>";
            $retour = true;
            
            if ($writeMailToLogs) {
                $this->saveMailToLogs(
                    array("envoyeur"=>$envoyeur,"destinataire"=>$destinataire,
                    "sujet"=>stripslashes($sujet),"message"=>$message,
                    "debug"=>true, 'logfile'=>$logfile)
                );
            }
        } else {
            $retour = pia_mail(
                $destinataire, stripslashes($sujet),
                wordwrap($message), $headers, null
            );
            
            if ($writeMailToLogs) {
                $this->saveMailToLogs(
                    array("envoyeur"=>$envoyeur,"destinataire"=>$destinataire,
                    "sujet"=>stripslashes($sujet),"message"=>$message,
                    "debug"=>false, 'logfile'=>$logfile)
                );
            }            
        }
        
        
        return $retour;
    }
    
    /**
     * Envoi d'un mail a tous les administrateurs
     * 
     * @param string  $envoyeur                 Emetteur
     * @param string  $sujet                    Sujet
     * @param string  $message                  Message
     * @param $string $criteres                 Criteres
     * @param bool    $writeMailToLogs          ?
     * @param bool    $isEnvoiRegroupeDesactive ?
     * @param string  $logfile                  Fichier de logs
     * 
     * @return void
     * */
    public function sendMailToAdministrators(
        $envoyeur='archi-strasbourg', $sujet='', $message='', $criteres='',
        $writeMailToLogs=false, $isEnvoiRegroupeDesactive=false, $logfile='mail.log'
    ) {
        $replyTo="";
        if (is_array($envoyeur)) {
            $replyTo = $envoyeur["replyTo"];
            $envoyeur = $envoyeur["envoyeur"];
        }
    
    
        // recherche les administrateurs
        $authentification = new archiAuthentification();
        $idUtilisateur = '0';
        if ($authentification->estConnecte()) {
            $idUtilisateur = $authentification->getIdUtilisateur();
        }
        
        // n'envoi pas le mail si l'utilisateur courant est lui meme admin
        $sqlNoSendToAdmin="";
        if ($authentification->estAdmin()) {
            $sqlNoSendToAdmin = "and idUtilisateur!='".$idUtilisateur."'";
        }
        
        /* Envoi aux admins dont le compte est actif
         * et la periode d'envoi est "immediate"
         * */
        if ($isEnvoiRegroupeDesactive) {
            $sql = "SELECT mail from utilisateur where idProfil='4'".
                " and compteActif='1' ".$criteres." ".$sqlNoSendToAdmin;    
        } else {
            $sql = "SELECT mail from utilisateur where idProfil='4' ".
                "and compteActif='1' and (idPeriodeEnvoiMailsRegroupes='1' ".
                "OR idPeriodeEnvoiMailsRegroupes='0') ".
                $criteres." ".$sqlNoSendToAdmin;    
        }
        
        $res = $this->connexionBdd->requete($sql);
        while ($fetch = mysql_fetch_assoc($res)) {
            $headers ='From: "'.$envoyeur.'"<'.$envoyeur.'>'.
                "\r\nReply-To: ".$replyTo.
                "\r\nContent-Type: text/html; charset=\"utf-8\"\r\n";
            if (isset($this->isSiteLocal) && $this->isSiteLocal == true) {
                echo "Envoi d'un mail aux administrateurs<br>";
                echo $headers;
                echo "<br>to : ".$fetch['mail']."<br>";
                echo "subject : $sujet<br>";
                echo "$message<br>";
                echo "finMail<br>";
                
                if ($writeMailToLogs) {
                    $this->saveMailToLogs(
                        array("envoyeur"=>$envoyeur,"destinataire"=>$fetch['mail'],
                        "sujet"=>$sujet,"message"=>$message,"debug"=>true,
                        'logfile'=>$logfile)
                    );
                }
                
            } else {
                pia_mail($fetch['mail'], $sujet, $message, $headers, null);
                if ($writeMailToLogs) {
                    $this->saveMailToLogs(
                        array("envoyeur"=>$envoyeur,"destinataire"=>$fetch['mail'],
                        "sujet"=>$sujet,"message"=>$message,
                        "debug"=>false, 'logfile'=>$logfile)
                    );
                }
            }
        }
    }
    
    /**
     * ?
     * 
     * @param string $email E-mail
     * @param string $name  Nom
     * 
     * @return string HTMl
     * */
    function encodeEmail($email, $name = null)
    {

        $email = preg_replace("/\"/", "\\\"", $email);

        if ($name == null) {
            $name = $email;
        }

        $old = "document.write('<a class=lien href=\"mailto:$email\">$name</a>')";

        $output = "";

        for ($i=0; $i < pia_strlen($old); $i++) {
            $output = $output . '%' . bin2hex(pia_substr($old, $i, 1));
        }

        $output = '<script type="text/javascript">eval(unescape(\''.
            $output.'\'))</script>';
        $output .= '<noscript><div>Vous devez accepter le Javascript'.
            ' pour voir l\'email</div></noscript>';
        return $output;
    }

    /**
     * ?
     * 
     * @param string $email E-mail
     * @param string $name  Nom
     * @param string $sujet Sujet
     * 
     * @return string HTMl
     * */
    function encodeEmailwithSubject($email, $name = null, $sujet="")
    {
        $email = preg_replace("/\"/", "\\\"", $email);

        if ($name == null) {
            $name = $email;
        }

        $old1 = "document.write('<a class=lien href=\"mailto:$email?subject=";
        //".$sujet."
        $old2= "\">$name</a>')";



        $output1 = "";

        for ($i=0; $i < pia_strlen($old1); $i++) {
            $output1 = $output1 . '%' . bin2hex(pia_substr($old1, $i, 1));
        }

        $output2 = "";

        for ($i=0; $i<pia_strlen($old2); $i++) {
            $output2 = $output2 .'%'.bin2hex(pia_substr($old2, $i, 1));
        }


        $output="";
        $output = '<script type="text/javascript">eval(unescape(\''.
            $output1.'\')+"'.$sujet.'"+unescape(\''.$output2.'\'))</script>';
        $output .= '<noscript><div>Vous devez accepter le Javascript'.
            ' pour voir l\'email</div></noscript>';
        return $output;
    }

    /**
     * ?
     * 
     * @param string $email E-mail
     * @param string $name  Nom
     * 
     * @return string HTML
     * */
    function encodeEmail_debutpage($email, $name = null)
    {

        $email = preg_replace("/\"/", "\\\"", $email);

        if ($name == null) {
            $name = $email;
        }

        $old = "document.write('<a CLASS=infoacceuil ".
            "href=\"mailto:$email\">$name</a>')";

        $output = "";

        for ($i=0; $i < pia_strlen($old); $i++) {
            $output = $output . '%' . bin2hex(pia_substr($old, $i, 1));
        }

        $output = '<script type="text/javascript">eval(unescape(\''.
            $output.'\'))</script>';
        $output .= '<noscript><div>Vous devez accepter le Javascript'.
            ' pour voir l\'email</div></noscript>';
        return $output;
    }
    
    /**
     * Effectue une sauvegarde du mail
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function saveMailToLogs($params = array())
    {
        if (isset($params['destinataire'])
            && isset($params['sujet'])
            && isset($params['message'])
            && isset($params['debug'])
        ) {
            $debugValue=false;
            if ($params['debug']==true) {
                $debugValue=true;
            }
                
            
            $message = strip_tags($params['message'], '<br>');
            if (file_exists($this->getCheminPhysique().'logs/'.$params['logfile'])) {
                $lastlog = popen(
                    'tac '.escapeshellcmd(
                        $this->getCheminPhysique().
                        'logs/'.$params['logfile']
                    ), 'r'
                );

                $lastline = json_decode(fgets($lastlog));
                pclose($lastlog);
            } else {
                $lastline[3]='';
            }

            if ($lastline[3] == $message) {
                $fn = $this->getCheminPhysique().'/logs/'.$params['logfile'];
                $size = filesize($fn);
                $block = 4096;
                $trunc = max($size - $block, 0);

                $f = fopen($fn, "c+");
                if (flock($f, LOCK_EX)) {
                    fseek($f, $trunc);
                    $bin = rtrim(fread($f, $block), "\n");
                    if ($r = strrpos($bin, "\n")) {
                        ftruncate($f, $trunc + $r + 1);
                    }
                }
                fclose($f);
                error_log(
                    json_encode(
                        array($lastline[0], array_merge(
                            $lastline[1],
                            array($params['destinataire'])
                        ), $params['sujet'], $message)
                    )
                    .PHP_EOL, 3, $this->getCheminPhysique().
                    '/logs/'.$params['logfile']
                );
            } else {
                error_log(
                    json_encode(
                        array(date('c'), array($params['destinataire']),
                        $params['sujet'], $message)
                    ).PHP_EOL, 3, $this->getCheminPhysique().
                    '/logs/'.$params['logfile']
                );
            }
        } else {
            echo "mailObject.class.php : ".
            "sauvegarde dans les logs impossible, il manque un champ.<br>";
        }
    }
}
?>
