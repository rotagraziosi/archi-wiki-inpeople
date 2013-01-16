<h1><?_("Liste de tous les commentaires")?></h1>

{pagination}
<div>
<h2>
<?_("Commentaires")?>
</h2>
<table border="" width='100%' style='padding:0px;margin:0px;border:0px;'>
<tr>
<td style='padding:0px;margin:0px;'>
<!-- BEGIN commentaires -->
<div class="commentWrapper">
<div class="commentHeader">{commentaires.pseudo}</div>
<div class="comment">{commentaires.commentaire}</div>
</div>
<!-- END commentaires -->
</td>
</tr>
</table>
</div>
{pagination}
