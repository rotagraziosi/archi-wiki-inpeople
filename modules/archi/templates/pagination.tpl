﻿{listeAlphabetique}


<a href="{urlPremier}" onclick="{onClickPremier}"> << </a>
<a href="{urlPrecedent}" onclick="{onClickPrecedent}"> <?_("Précédent")?> </a>

{pointillesPrecedents}

<!-- BEGIN pages -->
<!-- BEGIN isNotPageCourante -->
<a href="{pages.isNotPageCourante.url}" onclick="{pages.isNotPageCourante.onClick}">{pages.numero}</a>
<!-- END isNotPageCourante --> 

<!-- BEGIN isPageCourante -->
<b>{pages.numero}</b>
<!-- END isPageCourante -->
<!-- END pages -->

{pointillesSuivants}

<a href="{urlSuivant}" onclick="{onClickSuivant}"> <?_("Suivant")?> </a>
<a href="{urlDernier}" onclick="{onClickDernier}"> >> </a>
