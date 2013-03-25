<div width=750 style='padding-left:100px;'>
<h1>Inscription</h1>
<br>
    <div>
    <?_("L'inscription sur www.archi-strasbourg.org vous permet de contribuer au site en donnant des informations sur un immeuble, une adresse, en ajoutant des photos, ou en modifiant les articles existants. Vous pourrez aussi recevoir les nouvelles adresses par mail.")?>
    </div>
<br>
<form action='{ACTIONFORM}' name='formInscription' method='post' enctype='multipart/form-data'>
    <table cellspacing=0 cellpadding=0 border=0>
    <tr>
        <td class='enteteInscription borduresInscription'><?_("Nom")?></td><td class='borduresInscription'><input type='text' name='nom' value='{nom}'><font color=red>{nomErreur}</font></td>
    </tr>
    <tr>
        <td class='enteteInscription borduresInscription'><?_("Prénom")?></td><td class='borduresInscription'><input type='text' name='prenom' value='{prenom}'><font color=red>{prenomErreur}</font></td>
    </tr>
    <tr>
        <td class='enteteInscription borduresInscription'><?_("Adresse e-mail (qui sera votre identifiant pour la connexion)")?></td><td class='borduresInscription'><input type='text' name='mail' value='{mail}'><font color=red>{mailErreur}</font></td>
    </tr>
    <tr>
        <td class='enteteInscription borduresInscription'><?_("Mot de passe")?></td><td class='borduresInscription'><input type='password' name='mdp1' value=''><font color=red>{mdp1Erreur}</font></td>
    </tr>
    <tr>
        <td class='enteteInscription borduresInscription'><?_("Mot de passe (confirmation)")?></td><td class='borduresInscription'><input type='password' name='mdp2' value=''><font color=red>{mdp2Erreur}</font></td>
    </tr>
    <tr>
        <td class='enteteInscription borduresInscription bordureFinInscription' align='left'><img id="captcha" src="includes/securimage/securimage_show.php" alt="CAPTCHA Image" valign='middle' />&nbsp;<?_("Recopiez les caractères de l'image")?><br> <a href="#" onclick="document.getElementById('captcha').src = 'includes/securimage/securimage_show.php?' + Math.random(); return false" style='color:#FFFFFF;'><?_("Recharger")?></a>
        </td>
        <td class='borduresInscription bordureFinInscription'><input type="text" name="captcha_code" size="10" maxlength="6" />{captchaErreur}</td>
    </tr>
    </table>
<br>
    <input type='submit' value='<?_("Valider")?>' name='submitInscription'><br>
    <font color=red>{mdpDifferentsErreur}</font>
</form>
</div>
