{listeAlphabetique}


<a href="{urlPremier}" onclick="{onClickPremier}"> << </a>
<a href="{urlPrecedent}" onclick="{onClickPrecedent}"> <?_("Précédent")?> </a>

{pointillesPrecedents}

<!-- BEGIN pages -->
<!-- BEGIN isNotPageCourante -->
<a href="{pages.isNotPageCourante.url}" class="white" onclick="{pages.isNotPageCourante.onClick}">{pages.numero}</a>
<!-- END isNotPageCourante --> 

<!-- BEGIN isPageCourante -->
{pages.numero}
<!-- END isPageCourante -->
<!-- END pages -->

{pointillesSuivants}

<a href="{urlSuivant}" onclick="{onClickSuivant}"> <?_("Suivant")?> </a>
<a href="{urlDernier}" onclick="{onClickDernier}"> >> </a>
