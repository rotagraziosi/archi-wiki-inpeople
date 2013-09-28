<h1><?_("Utilisateur")?></h1>
<p><?_("Actions de l'utilisateur")?> <strong>{prenom} {nom}</strong></p>
<!-- BEGIN utilisateurCourantIsAdmin -->
<p><?_("Cet utilisateur a le rang :")?> <strong>{rang}</strong></p>
<!-- END utilisateurCourantIsAdmin -->
{rendreAdmin}

<form action="{formAction}" name="formUtilisateur" enctype="multipart/form-data" method='post'>
<!-- BEGIN detailUtilisateur -->
<input type='hidden' name='idUtilisateurModif' value='{idUtilisateurModif}' />
    <table>
    <caption><?_("Détails")?></caption>
    <tr><td><?_("Nom")?></td><td><input type='text' name='nom' value="{detailUtilisateur.nom}" /></td><td>{nom-error}</td></tr>
    <tr><td><?_("Prénom")?></td><td><input type='text' name='prenom' value="{detailUtilisateur.prenom}" /></td><td>{prenom-error}</td></tr>
    <tr><td><?_("Adresse e-mail")?></td><td><input type='text' name='mail' value='{detailUtilisateur.email}' /></td><td>{detailUtilisateur.email-error}</td></tr>
    <tr><td><?_("URL de votre site")?></td><td><input type='text' name='urlSiteWeb' value='{detailUtilisateur.urlSiteWeb}' /></td><td>{detailUtilisateur.urlSiteWeb-error}</td></tr>
    <tr><td valign=middle><?_("Votre avatar")?></td><td valign=middle><div style='float:left;padding-top:50px;'><INPUT type=hidden name=MAX_FILE_SIZE  VALUE=8388608><input type='file' name='fichierAvatar' value=''><br><?_("Taille de fichier max :")?> 5 Mo<br><?_("Cochez pour supprimer")?>&nbsp;<input type='checkbox' name='supprFichierAvatar' value='1'></div>{detailUtilisateur.imageAvatar}</td><td>{detailUtilisateur.imageAvatar-error}</td></tr>
    <tr><td><?_("Ville favorite")?></td><td>
    <input type='text' name="villetxt" id="villetxt" value="{detailUtilisateur.villetxt}" readonly>
    <input type='hidden' name="ville" id="ville" value="{detailUtilisateur.ville}">
    <input type='button' value="Choisir" onclick="{detailUtilisateur.onClickChoixVilleFavorite}">
    
    </td></tr>
        <!-- BEGIN utilisateurCourantIsAdmin -->
            <tr><td><?_("Profil")?></td><td>
                {selectProfil}
            </td><td>{utilisateurCourantIsAdmin.detailUtilisateur.estAdmin-error}</td></tr>
            <tr><td><?_("Alerte mail")?></td><td>
                <input type='radio' name='alerteMail' value='1' {checkAlerteMailOui} /> <?_("Oui")?>
                <input type='radio' name='alerteMail' value='0' {checkAlerteMailNon} /> <?_("Non")?>
            </td><td>{utilisateurCourantIsAdmin.detailUtilisateur.alerteMail-error}</td></tr>
        <tr><td><?_("Recevoir les alertes sur les commentaires")?></td><td>
                <input type='radio' name='alerteCommentaires' value='1' {checkAlertesCommentairesOui} /> <?_("Oui")?>
                <input type='radio' name='alerteCommentaires' value='0' {checkAlertesCommentairesNon} /> <?_("Non")?></td></tr>
        <tr><td><?_("Recevoir les alertes sur les adresses que j'ai créées")?></td><td>
                <input type='radio' name='alerteAdresses' value='1' {checkAlertesAdressesOui} /> <?_("Oui")?>
                <input type='radio' name='alerteAdresses' value='0' {checkAlertesAdressesNon} /> <?_("Non")?></td></tr>
        <tr><td><?_("Permettre à l'utilisateur de se faire contacter<br>sur son profil")?></td><td>
                <input type='radio' name='afficheFormulaireContactPersoProfilPublic' value='1' {checkContactPersoProfilOui} /> <?_("Oui")?>
                <input type='radio' name='afficheFormulaireContactPersoProfilPublic' value='0' {checkContactPersoProfilNon} /> <?_("Non")?></td></tr>
        <tr><td><?_("Permettre à l'utilisateur de renseigner le champ numero d'archive")?></td><td>
                <input type='radio' name='displayNumeroArchiveField' value='1' {checkDisplayNumeroArchiveFieldOui} /> <?_("Oui")?>
                <input type='radio' name='displayNumeroArchiveField' value='0' {checkDisplayNumeroArchiveFieldNon} /> <?_("Non")?></td></tr>
        <tr><td><?_("Permettre à l'utilisateur de renseigner le champs date fin (uniquement profil utilisateur)")?></td><td>
                <input type='radio' name='displayDateFinField' value='1' {checkDisplayDateFinFieldOui} /> <?_("Oui")?>
                <input type='radio' name='displayDateFinField' value='0' {checkDisplayDateFinFieldNon} /> <?_("Non")?></td></tr>
            <tr><td><?_("L'utilisateur peut mettre des images sous copyright")?></td><td>
            <input type='radio' name='canCopyright' value='1' {canCopyright1} id="canCopyright1" /> <label for="canCopyright1"><?_("Oui")?></label>
            <input type='radio' name='canCopyright' value='0' {canCopyright0} id="canCopyright0" /> <label for="canCopyright0"><?_("Non")?></label>
        </td></tr>
            <tr><td><?_("L'utilisateur peut modifier les tags des images")?></td><td>
            <input type='radio' name='canModifyTags' value='1' {canModifyTags1} id="canModifyTags1" /> <label for="canModifyTags1"><?_("Oui")?></label>
            <input type='radio' name='canModifyTags' value='0' {canModifyTags0} id="canModifyTags0" /> <label for="canModifyTags0"><?_("Non")?></label>
        </td></tr>
            <tr><td><?_("Permettre à l'utilisateur d'ajouter une adresse sans préciser la rue")?></td><td>
            <input type='radio' name='canAddWithoutStreet' value='1' {canAddWithoutStreet1} id="canAddWithoutStreet1" /> <label for="canAddWithoutStreet1"><?_("Oui")?></label>
            <input type='radio' name='canAddWithoutStreet' value='0' {canAddWithoutStreet0} id="canAddWithoutStreet0" /> <label for="canAddWithoutStreet0"><?_("Non")?></label>
        </td></tr>
        <!-- END utilisateurCourantIsAdmin -->
        <!-- BEGIN banissementUtilisateurParAdmin -->
        <tr><td><?_("Bannir l'utilisateur (le compte devient inactif)")?></td><td>
            <input type='radio' name='bannirUtilisateur' value='1' {checkDisplayBannirUtilisateurOui} /> <?_("Oui")?>
            <input type='radio' name='bannirUtilisateur' value='0' {checkDisplayBannirUtilisateurNon} /> <?_("Non")?>
        </td></tr>
        <!-- END banissementUtilisateurParAdmin -->
        <!-- BEGIN utilisateurCourantIsAdmin -->
        <tr>
            <td><?_("Périodicité de l'envoi de mails d'information")?></td>
            <td>{selectPeriodiciteMail}</td>
        </tr>
        <!-- END utilisateurCourantIsAdmin -->
        <tr><td><?_("Mot de passe")?></td><td><input type='password' name='mdp1' value='{mdp1}' /></td><td>{mdp1-error}</td></tr>
        <tr><td><?_("Mot de passe")?> (<?_("confirmation")?>)</td><td><input type='password' name='mdp2' value='{mdp2}' /></td><td>{mdp2-error}</td></tr>
        <tr><td></td><td><input type='submit' value='Modifier' /></td><td></td></tr>
    </table>
<!-- END detailUtilisateur -->
</form>


<!--
<h2>Images Modifées</h2>
<a href='{lienModifierImage}'>Modifier vos images</a>
{images}
<h2>Évènements Modifés</h2>
{evenements}
-->
{popupChoixVille}
