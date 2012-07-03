<br>
<h2><?_("Liste des commentaires")?></h2>
<table {tableHtmlCode}>
<!-- BEGIN commentaires -->
<tr><td style="padding:0;" itemprop="review" itemscope itemtype="http://schema.org/Review"><div class="commentHeader" itemprop="creator" itemscope itemtype="http://schema.org/Person">{commentaires.infosPersonne} {commentaires.boutonSupprimer}{commentaires.siteWeb}{commentaires.adresseMail}</div>
<div class="comment" itemprop="reviewBody">{commentaires.commentaire}</div></td></tr>
<!-- END commentaires -->
</table>
{msg}
