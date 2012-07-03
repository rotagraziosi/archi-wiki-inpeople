<form action="{formAction}" name="modifAdresseImage" enctype="multipart/form-data" method="POST">
<table>
<tr><td><?_("Liste des adresses liées à l'image")?></td></tr>
<!-- BEGIN adressesLiees -->
<tr><td>{adressesLiees.intitule}</td></tr>
<tr>
<td>se situe</td><td><input type="text" name="seSitue_{adressesLiees.idAdresse}txt" id="seSitue_{adressesLiees.idAdresse}txt" value="{adressesLiees.seSitueTxt}"><input type="hidden" name="seSitue_{adressesLiees.idAdresse}" id="seSitue_{adressesLiees.idAdresse}" value="{adressesLiees.seSitue}">
<a href="{adressesLiees.urlPopupSeSitue}" onclick="{adressesLiees.onClickPopupSeSitue}"><?_("Choisir")?></a>
</td><td>{adressesLiees.seSitue-error}</td>
</tr>
<tr>
<td>pris depuis</td><td><input type="text" name="prisDepuis_{adressesLiees.idAdresse}txt" id="prisDepuis_{adressesLiees.idAdresse}txt" value="{adressesLiees.prisDepuisTxt}"><input type="hidden" name="prisDepuis_{adressesLiees.idAdresse}" id="prisDepuis_{adressesLiees.idAdresse}" value="{adressesLiees.prisDepuis}">
<a href="{adressesLiees.urlPopupPrisDepuis}" onclick="{adressesLiees.onClickPopupPrisDepuis}"><?_("Choisir")?></a>
</td><td>{adressesLiees.prisDepuis-error}</td>
</tr>
<tr>
<td><?_("étage")?></td><td><input type="text" value="{adressesLiees.etage}" name="etage_{adressesLiees.idAdresse}"></td><td>{adressesLiees.etage-error}</td>
</tr>
<tr>
<td><?_("hauteur")?></td><td><input type="text" value="{adressesLiees.hauteur}" name="hauteur_{adressesLiees.idAdresse}"></td><td>{adressesLiees.hauteur-error}</td>
</tr>
<!-- END adressesLiees -->
<tr>
<td>
<input type='hidden' value="{listeIdAdresses}" name="listeIdAdresses">
<!-- BEGIN isConnected -->
<input type='submit' value="Modifier" name="Modifier">
<!-- END isConnected -->
</td>
</tr>
</table>
</form>

{popupAdresses}
