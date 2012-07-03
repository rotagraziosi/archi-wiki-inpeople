<h1><?_("Liste de tous les commentaires")?></h1>

{pagination}
<div style='border:2px solid #007799;width:750px;'>
<div id='titreEncartCommentaire' style='background-color:#558800;color:#FFFFFF;padding:2px;'>
<?_("Commentaires")?>
</div>
<table border="" width='100%' style='padding:0px;margin:0px;border:0px;'>
<tr>
<td style='padding:0px;margin:0px;'>
<!-- BEGIN commentaires -->
<div style='background-color:#E1E1E1;padding:2px;font-size:11px;display:block;overflow:visible;'>{commentaires.pseudo}</div>
<div style='padding:2px;font-size:11px;'>{commentaires.commentaire}</div>
<!-- END commentaires -->
</td>
</tr>
</table>
</div>
{pagination}
