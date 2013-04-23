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
require_once __DIR__.'/../includes/recaptcha-php-1.11/recaptchalib.php';

/**
 * Affichage du formulaire d'adhésion
 * 
 * @return void
 * */
function displayForm ()
{
    $defaultAmount = 20;
    $page=new archiPage(10, LANG);
    if (empty($page->title)) { 
        $page=new archiPage(10, Config::$default_lang);
    }

    echo '<h1>'.stripslashes($page->title).'</h1>';
    echo "<div>".stripslashes($page->content)."</div><br/>";
    echo '<script src="js/membership.js"></script>
    <form class="membership"
    action="index.php?archiAffichage=membership" method="post">';
    
    $fields = array(
        array('surname', _('Nom :'), true),
        array('name', _('Prénom :')),
        array('job', _('Profession/Société :')),
        array('address', _('Adresse :')),
        array('email', _('Email :'), true, 'email'),
        array('tel', _('Tél :')),
    );
    foreach ($fields as $field) {
        echo '<label for="'.$field[0].'">'.$field[1].'</label><br/>
            <input ';
        if (isset($field[2]) && $field[2]) {
            echo 'required';
        }
        if (isset($field[3])) {
            echo ' type="'.$field[3].'" ';
        }
        echo ' name="'.$field[0].'" id="'.$field[0].'" ';
        if (isset($_POST[$field[0]])) {
            echo 'value="'.$_POST[$field[0]].'"/>';
        }
        echo '<br/><br/>';
    }


    echo '<label for="comment">'._('Commentaire :').'</label><br/>
    <textarea name="comment" id="comment">';
    if (isset($_POST['comment'])) {
        echo $_POST['comment'];
    }
    echo '</textarea><br/><br/>
    <legend>'._('Cotisation :').'</legend>';
    
    $amounts = array(
        array(10, _(
            'Tarif réduit pour étudiants, bénéficiaires du RSA '.
            'et personnes non-imposables, sur justificatif'
        )),
        array(20, _(
            'Particulier'
        )),
        array(30, _(
            'Couple, famille'
        )),
        array(50, _(
            'Vous recevrez un reçu fiscal, '.
            'votre don ne vous coûtera que 30,20 euros.'
        )),
        array(80, _(
            'Vous recevrez un reçu fiscal, '.
            'votre don ne vous coûtera que 40,40 euros.'.PHP_EOL.
            'Si vous le souhaitez, vous pourrez figurer '.
            'sur notre liste de '
        ).
            "<a href='http://www.archi-strasbourg.org/index.php?".
            "archiAffichage=donateurs'>".
            'donateurs</a> '.
        _(
            'et pour une entreprise faire '.
            'apparaître votre logo et un lien sur le site de votre société.'
        ))
    );
    
    foreach ($amounts as $amount) {
        echo '<span title="'.$amount[1].'">
        <input required type="radio" name="amount" id="amount'.
        $amount[0].'" value="'.$amount[0].'" ';
        if ((isset($_POST['amount']) && $_POST['amount']==$amount[0]) 
            || (!isset($_POST['amount']) && $amount[0]==$defaultAmount)
        ) {
            echo 'checked';
        }
        echo ' />
        <label for="amount'.$amount[0].'">'.$amount[0].' €</label></span>'; 
    }
  
    echo '<span title="'.
    _(
        'Vous recevrez un reçu fiscal vous permettant de déduire 66 % '.
        'de votre don (somme supérieure à la cotisation de 20 euros).'
    ).'">
    <input type="radio" name="amount" value="other"/>
    <input type="number" name="otheramount" placeholder="Autre montant…" /></span>
    <br/>
    <div id="info_amounts" class="info_amounts"></div>';
    echo recaptcha_get_html('6LeXTOASAAAAACl6GZmAT8QSrIj8yBrErlQozfWE');
    
    echo '<input type="submit" />
    </form>';
}

if (isset($_POST['email'])) {
    $resp = recaptcha_check_answer(
        $config->captchakey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]
    );
    if ($resp->is_valid) {
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

        echo '<h1>'.stripslashes($page->title).'</h1>';
        echo "<div>".stripslashes($page->content)."</div><br/>";
    } else {
        echo '<div class="error">'._('Erreur : Captcha incorrect !').'</div>';
        displayForm();
    }
} else {
    displayForm();
}
