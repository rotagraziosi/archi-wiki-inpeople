<!Doctype HTML>
<html lang="{lang_short}">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>{titrePage}</title>
<meta name="description" content="{descriptionPage}" />
<meta name="robots" content="index follow, all" />
<meta name="keywords" content="<?_("architectures,architecture,neudorf,contades,centre,strasbourg,photos,immeubles,monuments,immobilier,alsace")?>{motsCle}" />
<link href="css/default.css" rel="stylesheet" type="text/css" />
<link href="css/persona-buttons.css" rel="stylesheet" type="text/css" />
<link rel="author" href="index.php?archiAffichage=contact" />
<link rel="author" href="mailto:{mailContact}" />
<link rel="author" href="{authorLink}" />
<!--dernieresAdresses, constructions, demolitions, actualites, culture-->
<link rel="alternate" href="rss.php?type=actualites" type="application/rss+xml" title="Actualités" />
<link rel="alternate" href="rss.php?type=dernieresAdresses" type="application/rss+xml" title="Nouvelles adresses" />
<link rel="alternate" href="rss.php?type=constructions" type="application/rss+xml" title="Derniers travaux" />
<link rel="alternate" href="rss.php?type=demolitions" type="application/rss+xml" title="Dernières démolitions" />
<link rel="alternate" href="rss.php?type=culture" type="application/rss+xml" title="Derniers événements culturels" />
<link rel="alternate" href="rss.php?type=dernieresVues" type="application/rss+xml" title="Dernières vues" />
<link rel="icon" href="favicon.png" type="image/png"  />
<script src='includes/datePicker.js'></script>
<script src='includes/bbcode.js'></script>
{ajaxFunctions}
{calqueFunctions}
<script src='includes/common.js'></script>
{jsHeader}
<!-- BEGIN utilisateurNonConnecte -->
<script src="https://browserid.org/include.js" type="text/javascript"></script>  
<script src="js/browserid.js" type="text/javascript"></script>  
<!-- END utilisateurNonConnecte -->
<script src="js/analytics.js" type="text/javascript"></script> 
<script src="js/jquery-ui/js/jquery-1.7.2.min.js"></script>
<script src="js/jquery-ui/js/jquery-ui-1.8.21.custom.min.js"></script>
<link href="js/jquery-ui/themes/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
<link rel="license" href="http://www.data.gouv.fr/Licence-Ouverte-Open-Licence">
</head>


<body onload="{onload}">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId=323670477709341";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="outer" class="outer">
    
    <div id="header" class="header">
    <!--<a href="./">
    <img src="images/logo_archi.png" alt="">
    </a>-->
    <div class="header2">
    <div class="inline-block homeTitle">
    <a href="./">
    <div class="title"><h1 class="h1"><img src="images/Advisa/logo-archi-strasbourg.png" alt="{titreSite}" /></h1><!--<h2 class="h2">Version 2.3</h2>--><?_("Architecture et histoire des bâtiments et lieux")?></div>
    </a>
    </div>
    <div class="infos"><a href="statistiques-adresses-photos-architectes-strasbourg.html">{infos}</a></div>
    </div>
    <!--
    <script>
    $(function() {
        $( "#progressbar" ).progressbar({
            value: 66
        });
    });
    </script>
    
    <div class='barredons'>
    <a href="actualites-archi-strasbourg-30.html">
    Dons :
    <div id='progressbar' class='progressbar'></div>
    <span class="percent">68 %</span><br/>
    <span class="euros">11850 € restants</span>
    </a>
    </div>
    -->
    
    <nav id="menu" class="menu">
            <div class="wrapper">
            
            <div class="leftpart">
            <ul>
            
            <!-- BEGIN isAdmin -->
            <li><a href="{listeUtilisateurs}" accesskey="2"><?_("Utilisateurs")?></a></li>
            
            <li><a href="{logsMails}"><?_("Logs Mails")?></a></li>
            <!-- END isAdmin -->
            
            <!-- BEGIN afficheAdministrationMenu -->
            <li><a href="{administration}"><?_("Administration")?></a></li>
            <!-- END afficheAdministrationMenu -->
            
            <!--<li><a href="{recherche}" accesskey="4" title=""><?_("Recherche")?></a></li>-->
            <li class="home"><a href="./" accesskey="1"><img src="images/Advisa/maison.png" alt="<?_("Accueil")?>" /></a></li>
            <li class="flag"><a href="?lang=fr_FR" title="Français">FR</a></li>
            <li class="flag"><a href="?lang=en_US"  title="English">EN</a></li>
            <li class="flag"><a href="?lang=de_DE"  title="Deutsch">DE</a></li>
            
            
            
            
        </ul>
        </div>
      
            
            <!-- BEGIN utilisateurNonConnecte -->
            <div class="loginForm">
            {formulaireConnexion}
            <ul class="nopadding">
            <li><a href="{urlMotDePasseOublie}"><?_("Mot de passe oublié ?")?></a></li>
            <li><a href="{urlInscriptDeconnexion}">{inscriptionDeconnexion}</a></li>
            </ul>
            </div>
            <!-- END utilisateurNonConnecte -->
            <!-- BEGIN utilisateurConnecte -->
            <div class="loginForm connected">
            <ul></ul>
            <ul class="connected">
            <li><a href="{urlMonProfil}">{txtMonProfil}</a></li>
            <li><a href="{urlMonArchi}">{txtMonArchi}</a></li>
            <li><a href="{urlInscriptDeconnexion}">{inscriptionDeconnexion}</a></li>
            </ul>
            </div>
            <!-- END utilisateurConnecte -->
            
              <div class="browserid">
              <!-- BEGIN utilisateurNonConnecte -->
        <a class="persona-button dark" id="browserid" title="<?_("Se connecter avec BrowserID")?>" title="<?_("Se connecter avec BrowserID")?>" ><span><?_("Connexion")?></span></a> 
        <!-- END utilisateurNonConnecte -->
        </div> 
        </div>
    </nav>
    </div>
    <div id="content" class="content">
        <div id="primaryContentContainer" class="primaryContentContainer">
            <div id="secondaryContent" class="secondaryContent">
                <div class="box boxA">
                    <!--<h2><?_("Rechercher")?></h2>-->
                    <div class="boxContent center">
                        {formulaireRecherche}
                        {lienRechercheParCarte}
                        {lienRechercheAvancee}
                    </div>
                </div>
                <div class="box">
                
                <nav class="boxContent">
                
                <ul>
                    {listPages}
                    <li><a href="{quiSommesNous}"><?_("Qui sommes nous ?")?></a></li>
                    <li><a href="{listeDossiers}"><?_("Adresses")?></a></li>
                    
                    <!-- BEGIN isParcours -->
                    <li><a href="{parcours}" title="parcours"><?_("Parcours")?></a></li>
                    <!-- END isParcours -->
                    
                    <li style='white-space:nowrap;'><a href="{ajoutNouveauDossier}"><?_("Ajouter une adresse")?></a></li>
                    <li style='white-space:nowrap;'><a href="{ajoutNouvellePersonne}"><?_("Ajouter une personne")?></a></li>
                    <li style='white-space:nowrap;'><a href="{nosSources}"><?_("Nos sources")?></a></li>
                    
                    <!--<li><a href="{publiciteMedias}"><?_("Les médias parlent de nous...")?></a></li>-->
                    <li><a href="index.php?archiAffichage=donateurs"><?_("Nos donateurs")?></a></li>
                    
                </ul>
                
                </nav>
                <div class="subbox paypal">
                <a href='index.php?archiAffichage=membership' >
                <?_("Devenir membre")?>
                </a>
                </div>
                <div class="subbox paypal">
                <a href='{faireUnDon}' >
                <!--<img src='https://www.paypalobjects.com/{lang}/i/btn/btn_donate_LG.gif' alt="-->
                <?_("Faire un don")?>
                <!--">-->
                </a>
                </div>
                <br/>
                <div class="fb-like-box reseauSocial" data-href="https://www.facebook.com/pages/Association-Archi-Strasbourg/215793091822502" data-width="190" data-stream="false" data-header="true"></div>
                <br/>
                <div class="reseauSocial">
                <!--
                <script charset="utf-8" src="https://widgets.twimg.com/j/2/widget.js"></script>
                <script>
                if (typeof TWTR !== 'undefined') {
                new TWTR.Widget({
                  version: 2,
                  type: 'profile',
                  rpp: 2,
                  interval: 30000,
                  width: 190,
                  height: 170,
                  theme: {
                    shell: {
                      background: '#819395',
                      color: '#D4E0E0'
                    },
                    tweets: {
                      background: '#ffffff',
                      color: '#8C8C8C',
                      links: '#6D89A1'
                    }
                  },
                  features: {
                    scrollbar: false,
                    loop: false,
                    live: false,
                    behavior: 'all'
                  }
                }).render().setUser('ArchiStrasbourg').start();
                }
                </script>
                -->
                <a data-chrome="nofooter noscrollbar" width="190" height="170" data-link-color="#6D89A1" class="twitter-timeline"  href="https://twitter.com/ArchiStrasbourg"  data-widget-id="339535482302119936">Tweets de @ArchiStrasbourg</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

                </div>
                <!--<A HREF="http://www.archi-strasbourg.org/?archiAffichage=faireUnDon"><IMG SRC="images/20120326appeldons/bandeau_pub_vertical.jpg" width="174" alt="<?_("Pour continuer à lire du contenu gratuit et de qualité, faites un don.")?>"></A>-->

                <div class="box boxB">
                {derniersCommentaires}
                </div>
                
                <!-- ajout fabien du 03/04/2012 nouveau bandeau de pub-->
                <div class="box licenceLogo">
                <a href="http://www.data.gouv.fr/Licence-Ouverte-Open-Licence" title="<?_("Le texte de ce site est disponible selon les termes de la Licence ouverte.")?>"><img src="images/Advisa/openlicence.png" alt="Licence ouverte"/>
                </a>
                </div>
                
                
                
                </div>
            </div>
            <div id="primaryContent" class="primaryContent" {microdata}>
            {GeoCoordinates}
            {bandeauPublicite}
            <div class="title breadcrumbs">{urlCheminSite}</div>
