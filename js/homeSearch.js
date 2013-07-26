/*jslint browser: true */
var switchSearchImg = function (e) {
    'use strict';
    e.preventDefault();
    document.getElementById('homeSearch').innerHTML = '<a href="index.php" id="switchSearchTxt">Texte</a> &mdash; <b>Images</b><form method="get" action="index.php"><input type="search" name="query" class="searchInput"><input type="hidden" value="imageSearch" name="archiAffichage"><input type="image" src="images/Advisa/loupe.png" class="loupe"><br><label for="licence">Licence&nbsp;:</label><input type="checkbox" name="licence_1" id="licence_1" checked=""><label for="licence_1">CC-BY-SA</label><input type="checkbox" name="licence_2" id="licence_2" checked=""><label for="licence_2">Domaine public</label><input type="checkbox" name="licence_3" id="licence_3" checked=""><label for="licence_3">Copyright</label></form>';
    document.getElementById('switchSearchTxt').addEventListener('click', switchSearchTxt, true);
    return false;
};
var switchSearchTxt = function (e) {
    'use strict';
    e.preventDefault();
    document.getElementById('homeSearch').innerHTML = '<div class="switchSearch"><b>Texte</b> — <a id="switchSearchImg" href="index.php?archiAffichage=imageSearch">Images</a></div><form method="get" action="index.php?archiAffichage=recherche"><input value="recherche" name="archiAffichage" type="hidden"><input value="Rechercher" name="submit" type="hidden"><input class="searchInput" placeholder="Rechercher" style="width:300px;" name="motcle" accesskey="F" type="text"><input value="Rechercher" class="loupe" name="submit" src="images/Advisa/loupe.png" type="image"><a href="index.php?archiAffichage=rechercheAvancee">Recherche avancée</a><br><span><input value="1" id="afficheResultatsSurCarte" name="afficheResultatsSurCarte" type="checkbox">&nbsp;<label for="afficheResultatsSurCarte">Afficher les résultats sur une carte</label></span></form>';
    document.getElementById('switchSearchImg').addEventListener('click', switchSearchImg, true);
    return false;
};

var initHomeSearch = function () {
    'use strict';
    document.getElementById('switchSearchImg').addEventListener('click', switchSearchImg, true);
};

window.addEventListener('load', initHomeSearch, false);
