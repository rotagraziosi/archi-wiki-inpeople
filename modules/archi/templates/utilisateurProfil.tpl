<table width=750><tr><td width=500><h2>{phraseBienvenu}</h2></td><td><img src='{srcImgTrombone}' border='0'></td></tr></table>

<!-- BEGIN utilisateurCourantIsAdmin -->
<p><?_("Cet utilisateur a le rang :")?> <strong>{rang}</strong></p>
<!-- END utilisateurCourantIsAdmin -->
{rendreAdmin}

<form action="{formAction}" name="formUtilisateur" enctype="multipart/form-data" method='post'>
<!-- BEGIN detailUtilisateur -->
<input type='hidden' name='idUtilisateurModif' value='{idUtilisateurModif}' />
	<table  style='font-size:12px;'>
	<tr><td><?_("Nom")?></td><td><input type='text' name='nom' value="{detailUtilisateur.nom}" /></td><td>{nom-error}</td></tr>
	<tr><td><?_("Prénom")?></td><td><input type='text' name='prenom' value="{detailUtilisateur.prenom}" /></td><td>{prenom-error}</td></tr>
	<tr><td><?_("E-mail")?></td><td><input type='text' name='mail' value='{detailUtilisateur.email}' /></td><td>{detailUtilisateur.email-error}</td></tr>
	<tr><td><?_("URL de votre site")?></td><td><input type='text' name='urlSiteWeb' value='{detailUtilisateur.urlSiteWeb}' /></td><td>{detailUtilisateur.urlSiteWeb-error}</td></tr>
	<tr><td valign=middle>Votre avatar</td><td valign=middle style='background-color:grey;'><div style='float:left;padding-top:50px;'><INPUT type=hidden name=MAX_FILE_SIZE  VALUE=8388608><input type='file' name='fichierAvatar' value=''><br><?_("Taille de fichier max : 5 Mo")?><br><?_("Cochez pour supprimer")?>&nbsp;<input type='checkbox' name='supprFichierAvatar' value='1'></div>{detailUtilisateur.imageAvatar}</td><td>{detailUtilisateur.imageAvatar-error}</td></tr>
	<tr><td><?_("Ville favorite")?></td><td>
	<input type='text' name="villetxt" id="villetxt" value="{detailUtilisateur.villetxt}" readonly>
	<input type='hidden' name="ville" id="ville" value="{detailUtilisateur.ville}">
	<input type='button' value="Choisir" onclick="{detailUtilisateur.onClickChoixVilleFavorite}">
	
	</td></tr>
		<!-- BEGIN utilisateurCourantIsAdmin -->
			<tr><td><?_("Profil")?></td><td>
				{selectProfil}
			</td><td>{utilisateurCourantIsAdmin.detailUtilisateur.estAdmin-error}</td></tr>
		<!-- END utilisateurCourantIsAdmin -->
			<tr><td><?_("Alerte mail des nouvelles adresses (1 par semaine)")?></td><td>
				<input type='radio' name='alerteMail' value='1' {checkAlerteMailOui} /> <?_("Oui")?>
				<input type='radio' name='alerteMail' value='0' {checkAlerteMailNon} /> <?_("Non")?>
			</td><td>{utilisateurCourantIsAdmin.detailUtilisateur.alerteMail-error}</td></tr>
		<tr><td><?_("Recevoir les alertes sur les commentaires")?></td><td>
				<input type='radio' name='alerteCommentaires' value='1' {checkAlertesCommentairesOui} /> <?_("Oui")?>
				<input type='radio' name='alerteCommentaires' value='0' {checkAlertesCommentairesNon} /> <?_("Non")?></td></tr>
		<tr><td><?_("Recevoir les alertes sur les adresses que j'ai créées")?></td><td>
				<input type='radio' name='alerteAdresses' value='1' {checkAlertesAdressesOui} /> <?_("Oui")?>
				<input type='radio' name='alerteAdresses' value='0' {checkAlertesAdressesNon} /> <?_("Non")?></td></tr>
		<!--<tr><td>permettre aux utilisateurs de vous contacter <br>personnellement sur votre profil<br>(dans tous les cas votre adresse mail n'apparaît pas)</td><td>
				<input type='radio' name='afficheFormulaireContactPersoProfilPublic' value='1' {checkContactPersoProfilOui} /> <?_("Oui")?>
				<input type='radio' name='afficheFormulaireContactPersoProfilPublic' value='0' {checkContactPersoProfilNon} /> <?_("Non")?></td></tr>		
		--><input type=hidden name='afficheFormulaireContactPersoProfilPublic' value='1'>
		<input type=hidden name='displayNumeroArchiveField' value='{displayNumeroArchiveField}'>
		<!-- BEGIN utilisateurCourantIsAdminOrModerateur -->
		<tr>
			<td><?_("Périodicité de l'envoi de mails d'information")?></td>
			<td>{selectPeriodiciteMail}</td>
		</tr>
		<!-- END utilisateurCourantIsAdminOrModerateur -->
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
