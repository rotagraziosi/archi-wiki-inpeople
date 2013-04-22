<?php
/**
 * Affiche le formulaire d'adhésion
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
if (isset($_POST['email'])) {
    $message = '
    Nom : '.$_POST['surname'].'<br/>
    Prénom : '.$_POST['name'].'<br/>
    Profession/Société : '.$_POST['job'].'<br/>
    Adresse : '.$_POST['address'].'<br/>
    Email : '.$_POST['email'].'<br/>
    Tél : '.$_POST['tel'].'<br/>
    Commentaire : '.$_POST['comment'].'<br/>';
    $message .='Cotisation : ';
    if (!empty($_POST['otheramount'])) {
        $message .=$_POST['otheramount'];
    } else {
        $message .=$_POST['amount'];
    }
    $message.='<br/>';
    
    $mail                       = new mailObject();
    $mail->sendMail(
        $mail->getSiteMail(), $mail->getSiteMail(),
        'Nouveau membre', $message, true
    );
    
    $page=new archiPage(11, LANG);
    if (empty($page->title)) { 
        $page=new archiPage(11, Config::$default_lang);
    }

    echo "<div>".stripslashes($page->content)."</div><br/>";
} else {
    $page=new archiPage(10, LANG);
    if (empty($page->title)) { 
        $page=new archiPage(10, Config::$default_lang);
    }

    echo "<div>".stripslashes($page->content)."</div><br/>";
    echo '<script src="js/membership.js"></script>
    <form class="membership"
    action="index.php?archiAffichage=membership" method="post">
    <label for="surname">'._('Nom :').'</label><br/>
    <input required name="surname" id="surname"/>
    <br/><br/>
    <label for="name">'._('Prénom :').'</label><br/>
    <input  name="name" id="name"/>
    <br/><br/>
    <label for="job">'._('Profession/Société :').'</label><br/>
    <input name="job" id="job"/><br/><br/>
    <label for="address">'._('Adresse :').'</label><br/>
    <input name="address" id="address"/><br/><br/>
    <label for="email">'._('Email :').'</label><br/>
    <input required type="email" name="email" id="email"/><br/><br/>
    <label for="tel">'._('Tél :').'</label><br/>
    <input name="tel" id="tel"/><br/><br/>
    <label for="comment">'._('Commentaire :').'</label><br/>
    <textarea name="comment" id="comment"></textarea><br/><br/>
    <legend>'._('Cotisation :').'</legend>
    <span title="'.
    _(
        'Tarif réduit pour étudiants, bénéficiaires du RSA '.
        'et personnes non-imposables, sur justificatif'
    ).'">
    <input required type="radio" name="amount" id="amount10" value="10"/>
    <label for="amount10">10 €</label></span>
    <span title="'._('Particulier').'">
    <input type="radio" name="amount" id="amount20" value="20"/>
    <label for="amount20">20 €</label></span>
    <span title="'._('Famille').'">
    <input type="radio" name="amount" id="amount30" value="30"/>
    <label for="amount30">30 €</label></span>
    <span title="'.
    _(
        'Vous recevrez un reçu fiscal, '.
        'votre don ne vous coûtera que 30,20 euros (50-20)*34%+20'
    ).'">
    <input type="radio" name="amount" id="amount50" value="50"/>
    <label for="amount50">50 €</label></span>
    <span title="'.
    _(
        'Si vous le souhaitez, vous pourrez figurer '.
        'sur notre liste de nos donateurset pour une entreprise faire '.
        'apparaître votre logo et un lien sur le site de votre société'
    ).'">
    <input type="radio" name="amount" id="amount80" value="80"/>
    <label for="amount80">80 €</label></span>
    <input type="radio" name="amount" value="other"/>
    <input type="number" name="otheramount" placeholder="Autre montant…" />
    <br/>
    <div id="info_amounts"></div>
    <input type="submit" />
    </form>';
}
