<?php
ini_set ('max_execution_time', 0);
//error_reporting(E_ALL | E_NOTICE);

include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/config.class.php');
include('../modules/archi/includes/archiImage.class.php');

echo '<h1>Connexion</h1>';
$mysqliNew = new mysqli("localhost", "archiv2", "fd89ind", "ARCHI_V2");

// Vérification de la connexion 
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}

$mysqliOld = new mysqli("localhost", "archiv2", "fd89ind", "archi_old");
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}


function nettoyeChaine ($string) {
	return stripslashes(htmlspecialchars(trim($string)));
}

function nettoyeChiffre ($string) {
	$return = htmlspecialchars(trim($string));
	$return = preg_replace('#[^0-9]#', $return, $return);
	return $return;
}

function nettoyeDate($string) {
	return $string;
}

function html2bb($html= '')
{
	$old = $html;
	$html = trim(stripslashes($html));
	$html =tidy_repair_string($html, array('output-xhtml' => true, 'show-body-only' => true, 'doctype' => 'strict', 'drop-font-tags' => true, 'drop-proprietary-attributes' => true, 'lower-literals' => true, 'quote-ampersand' => true, 'wrap' => 0), 'utf8');
	$html =trim($html);
	$html = preg_replace('!<a(.*)href=(.+)>(.+)</a>!isU', '[url=$2]$3[/url]', $html);
	$html = preg_replace('!(&lt;|<)a(.*)href=(.+)(&gt;|>)(.+)(&lt;|<)/a(&gt;|>)!isU', '[url=$3]$5[/url]', $html);
	$html = preg_replace('!<a(.*)>(.+)</a>!isU', '$2', $html);
	$html = preg_replace('!<a(.*)href=(.+)></a>!isU', '[url]$2[/url]', $html);
	$html = preg_replace('!(&lt;|<)br(.*)(&gt;|>)!isU', "\r\n", $html);
	$html = str_replace('<p>', "\r\n", $html);
	$html = str_replace('</p>', "", $html);
	return $html;
}

echo 'ok';

echo '<h2>connexion unicode</h2>';
$mysqliNew->query("SET NAMES 'utf8'");
$mysqliOld->query("SET NAMES 'utf8'");
echo 'ok';


/**************

	PAYS

**************/

echo '<h2>Pays</h2>';
$tabPays=array();

if ($resOld = $mysqliOld->query('SELECT * FROM pays where idPays=1 or idPays=3')) // modif laurent : on ne rapatrie que le pays France et Allemagne
{
	$mysqliNew->query('TRUNCATE TABLE `pays`');
	$stmt = $mysqliNew->prepare("INSERT INTO pays (idPays, nom) VALUES ('',?)") or die($mysqliNew->error);
	$stmt->bind_param("s", $nom) or die($mysqliNew->error);
	while ($rep = $resOld->fetch_object()) {
	echo $rep->nompays;
		$nom = nettoyeChaine($rep->nompays);
		$stmt->execute();
		$tabPays[$nom] = $mysqliNew->insert_id;
	}
	$stmt->close();
	//* Libère de résultat 
	$resOld->close();
}
echo 'ok';

//**************

//	VILLE

//**************

echo '<h2>Ville</h2>';
$tabVille = array();
if ($resOld = $mysqliOld->query("
						SELECT v.idville, v.nomville, v.codepostal,p.nompays 
						FROM ville v 
						LEFT JOIN pays p ON v.idpays=p.idpays
						WHERE substring(v.codePostal,1,2)='67'
						or v.nomVille = 'Kehl' 
						"))  // on ne rapatrie que les villes du 67 et kehl
{
	$mysqliNew->query('TRUNCATE TABLE `ville`');
	$stmt  = $mysqliNew->prepare("INSERT INTO ville (idVille, nom, idPays, codePostal) VALUES ('',?, ?, ?)");
	$stmt->bind_param("sii", $nom, $idPays, $codePostal);
	while ($rep = $resOld->fetch_object()) {
		$nom        = nettoyeChaine($rep->nomville);
		$nomPays    = nettoyeChaine($rep->nompays);
		$codePostal = nettoyeChiffre($rep->codepostal);
		if (isset($tabPays[$nomPays])) {
			if (isset($tabVille[$nom])) {
				echo '<p>La ville '.$nom.' existe déjà ...</p>';
				$tabVilleId[$rep->idville] = $tabVille[$nom];
			}
			else {
				$idPays = $tabPays[$nomPays];
				$stmt->execute();
				$tabVille[$nom] = $mysqliNew->insert_id;
				$tabVilleId[$rep->idville] = $mysqliNew->insert_id;
			}
		}
		else {
			echo '<p>le pays '.$nomPays.' n\'existe pas !</p>';
		}
	}
	$stmt->close();
	/* Libère de résultat */
	$resOld->close();
}
echo 'ok';

//**************

//	QUARTIER

//**************

echo '<h2>Quartiers</h2>';
$tabQuartier= array();
$tabQuartierId = array();
if ($resOld = $mysqliOld->query("
						SELECT q.idquartier, q.nomquartier, v.nomville 
						FROM quartier q 
						LEFT JOIN ville v ON v.idville=q.idville
						WHERE substring(v.codePostal,1,2)='67' or v.nomVille = 'Kehl'
						")) 
{
	$mysqliNew->query('TRUNCATE TABLE `quartier`');
	$stmt  = $mysqliNew->prepare("INSERT INTO quartier (idQuartier, nom, idVille, codePostal) VALUES ('',?, ?, '')");
	$stmt->bind_param("si", $nom, $idVille);
	while ($rep = $resOld->fetch_object()) {
		$nom        = nettoyeChaine($rep->nomquartier);
		$nomVille   = nettoyeChaine($rep->nomville);
		if (isset($tabVille[$nomVille])) {
			$idVille = $tabVille[$nomVille];
			$stmt->execute();
			$tabQuartier[$nom] = $mysqliNew->insert_id;
			$tabQuartierId[$rep->idquartier] = $mysqliNew->insert_id;
		}
		else {
			echo '<p>La ville '.$nomVille.' n\'existe pas !</p>';
		}
	}
	$stmt->close();
	/* Libère de résultat */
	$resOld->close();
}
echo 'ok';

//**************

//	SOUS-QUARTIER

//**************

echo '<h2>Sous-Quartiers</h2>';
$tabSousQuartier= array();
$tabSousQuartierId = array();
if ($resOld = $mysqliOld->query("
						SELECT sq.idsousquartier, q.nomquartier, sq.nomsousquartier 
						FROM sousquartier sq 
						LEFT JOIN quartier q ON sq.idquartier=q.idquartier
						LEFT JOIN ville v ON v.idville=q.idville
						WHERE substring(v.codePostal,1,2)='67' or v.nomVille = 'Kehl'
						")) 
{
	$mysqliNew->query('TRUNCATE TABLE `sousQuartier`');
	$stmt  = $mysqliNew->prepare("INSERT INTO sousQuartier (idSousQuartier, nom, idQuartier) VALUES ('',?, ?)");
	$stmt->bind_param("si", $nom, $idQuartier);
	while ($rep = $resOld->fetch_object()) {
		$nom         = nettoyeChaine($rep->nomsousquartier);
		$nomQuartier = nettoyeChaine($rep->nomquartier);
		if (isset($tabQuartier[$nomQuartier])) {
			$idQuartier = $tabQuartier[$nomQuartier];
			$stmt->execute();
			$tabSousQuartier[$nom] = $mysqliNew->insert_id;
			$tabSousQuartierId[$rep->idsousquartier] = $mysqliNew->insert_id;
		}
		else {
			echo '<p>Le quartier '.$nomQuartier.' n\'existe pas !</p>';
		}
	}
	$stmt->close();
	/* Libère de résultat */
	$resOld->close();
}
echo 'ok';

//**************

//	CREATION DES ENREGISTREMENTS PAR DÉFAUT

//**************

echo '<h2>Ajout des informations manquantes</h2>';


echo '<h3>Pays sans villes</h3>';
if ($res = $mysqliNew->query('SELECT p.idPays FROM pays p')) {
	$stmt = $mysqliNew->prepare("INSERT INTO ville (idVille, nom, idPays, codePostal ) VALUES ('', 'autre', ?, '')");
	$stmt->bind_param("i", $idPays);
	while($rep = $res->fetch_object()) {
		$idPays = $rep->idPays;
		$stmt->execute();
	}
	$stmt->close();
	$res->close();
}

echo '<h3>Villes sans Quartier</h3>';
if ($res = $mysqliNew->query('SELECT v.idVille FROM ville v')) {
	$stmt = $mysqliNew->prepare("INSERT INTO quartier (idQuartier, nom, idVille, codePostal ) VALUES ('', 'autre', ?, '')");
	$stmt->bind_param("i", $idVille);
	while($rep = $res->fetch_object()) {
		$idVille = $rep->idVille;
		$stmt->execute();
	}
	$stmt->close();
	$res->close();
}

echo '<h3>Quartiers sans Sous-Quartier</h3>';
if ($res = $mysqliNew->query('SELECT q.idQuartier FROM quartier q')) {
	$stmt = $mysqliNew->prepare("INSERT INTO sousQuartier (idSousQuartier, nom, idQuartier) VALUES ('', 'autre', ?)");
	$stmt->bind_param("i", $idQuartier);
	while($rep = $res->fetch_object()) {
		$idQuartier = $rep->idQuartier;
		$stmt->execute();
	}
	$stmt->close();
	$res->close();
}

echo 'ok';

//**************

//	RUE & ADRESSE

//**************


echo '<h2>Rues</h2>';
$tabAdresseId = array();

if ($resOld = $mysqliOld->query("
				SELECT a.nomadresse, a.complement, a.idadresse , v.nomville, q.nomquartier, sq.nomsousquartier  
				FROM adresse a 
				LEFT JOIN ville v ON v.idville = a.idville 
				LEFT JOIN quartier q ON q.idquartier = a.idquartier
				LEFT JOIN sousquartier sq ON sq.idsousquartier = a.idsousquartier
				WHERE substring(v.codePostal,1,2)='67' or v.nomVille = 'Kehl'
				ORDER BY a.idadresse ASC
				")) 
{
	$mysqliNew->query('TRUNCATE TABLE `rue`');
	$mysqliNew->query('TRUNCATE TABLE `historiqueAdresse`');

	$stmt  = $mysqliNew->prepare("INSERT INTO rue (idRue, idSousQuartier, nom, prefixe) VALUES ('',?, ?, ?)");
	$stmt->bind_param("iss", $idSousQuartier, $nom, $prefixe); // modif laurent pour ajout du prefixe
	
	$stmtTrouveIdSousQuartier  = $mysqliNew->prepare("SELECT sq.idSousQuartier FROM sousQuartier sq WHERE nom=? LIMIT 1");
	$stmtTrouveIdSousQuartier->bind_param("s", $nomSousQuartier);
	
	$stmtTrouveIdQuartier  = $mysqliNew->prepare("SELECT q.idQuartier FROM quartier q WHERE nom=? LIMIT 1");
	$stmtTrouveIdQuartier->bind_param("s", $nomQuartier);
	
	$stmtTrouveIdVille  = $mysqliNew->prepare("SELECT v.idVille FROM ville v WHERE nom=? LIMIT 1");
	$stmtTrouveIdVille->bind_param("s", $nomVille);
	$nbrue = 0;
	$nbadresse = 0;
	while ($rep = $resOld->fetch_object()) 
	{
		$complement = nettoyeChaine($rep->complement);
		$nom        = nettoyeChaine(nettoyeChaine($rep->nomadresse));//$complement.' '. modif laurent
		$prefixe	= $complement;
		
		if(!empty( $rep->nomsousquartier)) 
		{
			$nomSousQuartier = nettoyeChaine($rep->nomsousquartier);
			$stmtTrouveIdSousQuartier->execute() or die($stmt->error);
			$stmtTrouveIdSousQuartier->bind_result($idSousQuartier);
			if ($stmtTrouveIdSousQuartier->fetch()) 
			{
				$stmtTrouveIdSousQuartier->free_result();
				$stmt->execute() or die($stmt->error);
				$tabAdresseId[$rep->idadresse] = array('rue', $mysqliNew->insert_id);
			}
			else 
			{
				echo '<p>aucun sous quartier correspondant à '.$nomSousQuartier.'</p>';
				$tabAdresseId[$rep->idadresse] = array('erreur', $mysqliNew->insert_id);
			}
		}
		else if (!empty( $rep->nomquartier)) // le quartier est défini, faut trouver un sous quartier
		{
			$nomQuartier = nettoyeChaine($rep->nomquartier);
			$stmtTrouveIdQuartier->execute() or die($stmt->error);
			$stmtTrouveIdQuartier->bind_result($idQuartier);
			if ($stmtTrouveIdQuartier->fetch()) 
			{
				$stmtTrouveIdQuartier->free_result();
				$result = $mysqliNew->query('SELECT idSousQuartier FROM sousQuartier sq WHERE idQuartier='.$idQuartier.' AND nom="autre"');
				$reponse = $result->fetch_object();
				$idSousQuartier = $reponse->idSousQuartier;
				$stmt->execute() or die($stmt->error);
				$tabAdresseId[$rep->idadresse] = array('rue', $mysqliNew->insert_id);
			}
			else 
			{
				echo '<p>aucun quartier correspondant à '.$nomQuartier.'</p>';
				$tabAdresseId[$rep->idadresse] = array('erreur', $mysqliNew->insert_id);
			}
		}

		// la ville est définie, c'est une nouvelle adresse
		else if (!empty( $rep->nomville)) 
		{
			$nomVille  = nettoyeChaine($rep->nomville);
			$stmtTrouveIdVille->execute() or die($stmt->error);
			$stmtTrouveIdVille->bind_result($idVille);
			if ($stmtTrouveIdVille->fetch()) 
			{
				$stmtTrouveIdVille->free_result();
				$result = $mysqliNew->query('SELECT idSousQuartier FROM sousQuartier sq WHERE idQuartier=(SELECT idQuartier FROM quartier sq WHERE idVille='.$idVille.' AND nom="autre") AND nom="autre"');
				$reponse = $result->fetch_object();
				$nom = $mysqliNew->escape_string($nom);
				$prefixe = $mysqliNew->escape_string($prefixe); // ajout laurent : pour separation prefixe (complement) du nom de la rue
				$idSousQuartier = $reponse->idSousQuartier;
				$stmt->execute() or die($stmt->error);
				$tabAdresseId[$rep->idadresse] = array('rue', $mysqliNew->insert_id);
			}
			else 
			{
				echo '<p>aucun quartier correspondant à '.$nomQuartier.'</p>';
				$tabAdresseId[$rep->idadresse] = array('erreur', $mysqliNew->insert_id);
			}
		}
		else 
		{
			echo '<p>PERTE : idadresse = '.$rep->idadresse.'</p>';
			$tabAdresseId[$rep->idadresse] = array('erreur', $mysqliNew->insert_id);
		}
	}
	$stmt->close();
	// Libère de résultat
	$resOld->close();
}

echo $nbrue.'/'.$nbadresse.'ok';

//**************

//	PERSONNE

//**************

echo '<h2>Personnes</h2>
<p>PERTE : aucune adresse ni numéro de téléphone</p>';
if ($resOld = $mysqliOld->query('SELECT tp.libelle as metier, p.nompersonne, p.prenompersonne, p.datenaissance, p.datedeces, p.commentairesPersonne FROM personne p LEFT JOIN typepersonne tp ON tp.idtypepersonne=p.idtypepersonne')) {
	$mysqliNew->query('TRUNCATE TABLE `personne`');
	$mysqliNew->query('TRUNCATE TABLE `metier`');
	$mysqliNew->query('INSERT INTO metier (nom) VALUES (\'architecte\')');
	while ($rep = $resOld->fetch_object()) 
	{
		$idMetier      = 1;
		$nom           = $mysqliNew->escape_string(nettoyeChaine($rep->nompersonne));
		$prenom        = $mysqliNew->escape_string(nettoyeChaine($rep->prenompersonne));
		$dateNaissance = $mysqliNew->escape_string(nettoyeDate(  $rep->datenaissance));
		$dateDeces     = $mysqliNew->escape_string(nettoyeDate(  $rep->datedeces));
		$description   = $mysqliNew->escape_string(nettoyeChaine($rep->commentairesPersonne));
		$sql = "INSERT INTO personne (idMetier, nom, prenom, dateNaissance, dateDeces, description)  
			VALUES (".$idMetier.",'".$nom."','".$prenom."','".$dateNaissance."','".$dateDeces."','".$description."')";
		$mysqliNew->query($sql) or die ($mysqliNew->error);
	}
	// Libère de résultat 
	$resOld->close();
}
echo 'ok';


//**************

//	TYPE COURANT ARCHITECTURAL

//**************

echo '<h2>Courant architectural</h2>';
$tabCourantArchitectural = array();
if ($resOld = $mysqliOld->query('SELECT tca.idtypecourantarchitecture, tca.libelle FROM typecourantarchitecture tca')) {
	$mysqliNew->query('TRUNCATE TABLE `courantArchitectural`');
	while ($rep = $resOld->fetch_object()) {
		$nom   = $mysqliNew->escape_string(nettoyeChaine($rep->libelle));
		$sql = "INSERT INTO courantArchitectural (idCourantArchitectural, nom)  
			VALUES ('','".$nom."')";
		$mysqliNew->query($sql) or die ($mysqliNew->error);
		$tabCourantArchitectural[$rep->idtypecourantarchitecture] = $mysqliNew->insert_id;

	}
	$resOld->close();
}
echo 'ok';


//**************

//	Utilisateurs

//**************

echo '<h2>Utilisateur</h2>
<p>PERTE : pas de tel, pas de redimension auto, uniquement Fabien est ajouté</p>';
//$sql = "INSERT INTO utilisateur (idUtilisateur, nom, prenom, mail, motDePasse, estAdmin, alerteMail) 
//	VALUES ('','Romary','Fabien', 'fabien.romary@partenaireimmo.com', 'test', 1, 1)";
//$mysqliNew->query($sql) or die ($mysqliNew->error);
//$idUtilisateurFabien = $mysqliNew->insert_id;
$idUtilisateurFabien = 6;
echo 'ok';


//**************

//	Type Structure

//**************

echo '<h2>Type Structure</h2>';
$tabStructureId = array();
if ($resOld = $mysqliOld->query('SELECT ti.idtypeimage, ti.nomtypeimage FROM typeimage ti')) {
	$mysqliNew->query('TRUNCATE TABLE `typeStructure`');
	while ($rep = $resOld->fetch_object()) {
		$nom   = $mysqliNew->escape_string(nettoyeChaine($rep->nomtypeimage));
		$sql = "INSERT INTO typeStructure (idTypeStructure, nom)  
			VALUES ('', '".$nom."')";
		$mysqliNew->query($sql) or die ($mysqliNew->error);
		$tabStructureId[ $rep->idtypeimage ] = $mysqliNew->insert_id;
	}

	// structure par défaut :
	$nom = 'autre';
	$sql = "INSERT INTO typeStructure (idTypeStructure, nom)  
		VALUES ('', '".$nom."')";
	$mysqliNew->query($sql) or die ($mysqliNew->error);
	$tabStructureId['defaut'] = $mysqliNew->insert_id;
	// Libère de résultat 
	$resOld->close();
}
echo 'ok';


//**************

//	ÉVÈNEMENTS

//**************
 
echo '<h2>Évènements</h2>';
$tabEvenementId = array();
if ($resOld = $mysqliOld->query("
				SELECT d.idtypecourantarchitecture, 
				d.anneeconstruction,
				d.iddossierpere, 
				d.idsousquartier, 
				d.idquartier, 
				d.idville, 
				d.numerovoie, 
				d.idadresse, 
				d.iddossier, 
				d.idtypeimage, 
				d.datedossier, 
				d.titredossier, 
				c.textecommentaire, 
				d.commentaires AS description,
				CONCAT(a.complement,' ', a.nomadresse) AS nomAdresse
				
				FROM dossier d
				LEFT JOIN commentaire c ON c.iddossier = d.iddossier
				LEFT JOIN adresse a ON  a.idAdresse = d.idAdresse
				WHERE d.idtypedossier=1"
		
		) OR die($mysqliOld->error)
	) 
{
	
	$mysqliNew->query('TRUNCATE TABLE `historiqueEvenement`');
	$mysqliNew->query('TRUNCATE TABLE `historiqueAdresse`');
	$mysqliNew->query('TRUNCATE TABLE `_adresseEvenement`');
	$mysqliNew->query('TRUNCATE TABLE `_evenementEvenement`');
	$mysqliNew->query('TRUNCATE TABLE `_adresseImage`'); // ajout laurent pour vider aussi la tableau de liaison entre adresse et image
	$mysqliNew->query('TRUNCATE TABLE `_evenementCourantArchitectural`');
	$mysqliNew->query('TRUNCATE TABLE `source`');
	//$mysqliNew->query('TRUNCATE TABLE `typeSource`'); // modif laurent : on ne vide pas la table type de source
	
	$stmt  = $mysqliNew->prepare("INSERT INTO historiqueEvenement (idHistoriqueEvenement, idEvenement, dateDebut, dateFin, idTypeEvenement, idTypeStructure, titre, idUtilisateur, description, idSource, dateCreationEvenement )  
	                        VALUES ('', ?, ?, ?,1, ?, ?, ?, ?, 0, ?)") or die('*'.$mysqliNew->error);
	$stmt->bind_param('issisiss', $idEvenement, $dateDebut, $dateFin, $idTypeStructure, $titre, $idUtilisateur, $description, $dateCreationEvenement ) or die($mysqliNew->error);
	
	
	$stmtHistoriqueAdresse  = $mysqliNew->prepare("INSERT INTO historiqueAdresse (idHistoriqueAdresse, idAdresse, date, description, nom, numero,  idRue, idSousQuartier, idQuartier, idPays, idVille, idIndicatif ) 
	                        VALUES ('', ?, NOW(), '', ?, ?, ?, ?, ?, ?, ?, ?)") or die('*'.$mysqliNew->error);
	$stmtHistoriqueAdresse->bind_param('isiiiiiii', $idAdresse, $nom, $numero, $idRue, $idSousQuartier, $idQuartier, $idPays, $idVille,$idIndicatif ) or die($mysqliNew->error);
	
	$stmtLienEvenementAdresse = $mysqliNew->prepare("INSERT INTO _adresseEvenement (idEvenement, idAdresse) VALUES (?, ?)") or die ($mysqliNew->error);
	$stmtLienEvenementAdresse->bind_param('ii', $idEvenement, $idAdresseLien);
	
	$stmtLienEvenementCourant = $mysqliNew->prepare("INSERT INTO _evenementCourantArchitectural (idEvenement, idCourantArchitectural) VALUES (?, ?)") or die ($mysqliNew->error);
	$stmtLienEvenementCourant->bind_param('ii', $idEvenement, $idCourantArchitectural);
	
	
	//$mysqliNew->query('INSERT INTO typeSource (nom) VALUES ("Sans type")')  or die ($mysqliNew->error);
	//$mysqliNew->query('INSERT INTO source (nom, idTypeSource, description) VALUES ("Source à définir", 1, "Sans source pour l\'instant !")') or die ($mysqliNew->error); // modif laurent : pas besoin d'un element 'sans type'

	$idEvenement   = 0;
	$idUtilisateur = $idUtilisateurFabien;
	$idAdresse     = 0;
	while ($rep = $resOld->fetch_object()) 
	{
		echo "dossier:".$rep->iddossier."<br>";
		//
		// informations génériques
		$idEvenement += 1;
		
		if($rep->anneeconstruction!='')
		{
			$dateDebut = $rep->anneeconstruction."-00-00";  // modif laurent
		}
		else
		{
			$dateDebut    = $rep->datedossier;
		}
		
		$dateCreationEvenement = $rep->datedossier;
		$dateFin      = '';
		
		if (isset($tabStructureId[$rep->idtypeimage]))
			$idTypeStructure = $tabStructureId[$rep->idtypeimage];
		else
			$idTypeStructure = $tabStructureId['defaut'];
		

		// nom dossier
		if (empty($rep->titredossier))
		{
			//$titre = nettoyeChaine($rep->numerovoie.' '.$rep->nomAdresse);
			$titre="";
		}
		else
		{
			$titre = nettoyeChaine($rep->titredossier);
		}

		$titre       = html2bb($titre);
		$description = html2bb(nettoyeChaine($rep->description .' '.$rep->textecommentaire));

		$idCourantArchitectural = $rep->idtypecourantarchitecture;
		if (!empty($idCourantArchitectural))
		{
			$idCourantArchitectural = $tabCourantArchitectural[$idCourantArchitectural];
			$stmtLienEvenementCourant->execute() or die ($mysqliNew->error);
		}
		
		//
		// ENREGISTREMENT DU DOSSIER
		// 
		$stmt->execute() or die ($mysqliNew->error);
		

		// enregistrement dans la table de correspondance
		$tabEvenementId[$rep->iddossier] = $mysqliNew->insert_id;
		echo "=>".$tabEvenementId[$rep->iddossier]."<br>";
		// enregistrement de la liaison à créer avec les anciens ID
		// les nouveaux ID ne sont pas toujours connus
		if (!empty($rep->iddossierpere))
		{
			$tabEvenementALier[] = array($rep->iddossierpere,$rep->iddossier);
		}
		

		
		
		
		/////////
		/////////    ADRESSES
		/////////


		if (isset($rep->idadresse))
		{
			// enregistrement d'une adresse simple
			$idAdresse += 1;
			$nom = $titre;
			$numero = 0;
			$idIndicatif = 0;
			if (isset($rep->numerovoie))
			{
				if(is_numeric($rep->numerovoie))
				{
					$numero = $rep->numerovoie;
				}
				else
				{
					// modif laurent : on separe la partie numerique de ce qui doit etre l'information supplémentaire derriere le numero => l'indicatif
					$arraySplitIndicatifNumero = splitIndicatifFromNumero($rep->numerovoie,$mysqliNew);
					
					$numero = $arraySplitIndicatifNumero['numero'];
					$idIndicatif = $arraySplitIndicatifNumero['idIndicatif'];
					
				}
			}
				
			$idRue = $tabAdresseId[$rep->idadresse][1];
			$idSousQuartier = 0;
			$idQuartier = 0;
			$idPays  = 0;
			$idVille = 0;
			$idAdresseLien = $idAdresse;
			$stmtHistoriqueAdresse->execute() or die ($mysqliNew->error);
			$stmtLienEvenementAdresse->execute() or die ($mysqliNew->error);
			
		}
		elseif (isset($rep->idville)) {
//			echo '<p>ville : '.$rep->iddossier.' - '.$rep->idville.'</p>';
			if(isset($tabVilleId[$rep->idville]) && $rep->idville=='21') // creation d'une adresse pour les infos concernant la ville de kehl
			{
				$sql = 'SELECT idAdresse FROM historiqueAdresse WHERE idRue="" AND idSousQuartier="" AND  idQuartier="" AND idVille="'.$tabVilleId[$rep->idville].'" AND idPays="" LIMIT 1';
				$result = $mysqliNew->query($sql) or die ($mysqliNew->error);
				$reponse = $result->fetch_object();
				if (isset($reponse->idAdresse)) {
					$idAdresseLien = $reponse->idAdresse;
				}
				else {
					$idAdresse += 1;
					$nom    = '';
					$numero = 0;
					$idRue  = 0;
					$idSousQuartier = 0;
					$idQuartier = 0;
					$idPays  = 0;
					$idVille = $tabVilleId[$rep->idville];
					$stmtHistoriqueAdresse->execute() or die ($mysqliNew->error);
					$idAdresseLien = $idAdresse;
				}
				
				$stmtLienEvenementAdresse->execute() or die ($mysqliNew->error);
			}
		}
		
		
		
		
		
		
		
		
		
		/*
		if (!empty($rep->idsousquartier))
		{
			// echo '<p>sous-quartier : '.$rep->iddossier.' - '.$rep->idsousquartier.'</p>';
			// var_dump($tabSousQuartierId);
			$sql = 'SELECT idAdresse FROM historiqueAdresse WHERE idRue="" AND idSousQuartier="'.$tabSousQuartierId[$rep->idsousquartier].'" AND  idQuartier="" AND idVille="" AND idPays="" LIMIT 1';
			$result = $mysqliNew->query($sql) or die ($mysqliNew->error);
			$reponse = $result->fetch_object();
			if (isset($reponse->idAdresse)) {
				$idAdresseLien = $reponse->idAdresse;
			}
			else {
				$idAdresse += 1;
				$nom = '';
				$numero = 0;
				$idRue = 0;
				$idSousQuartier = $tabSousQuartierId[$rep->idsousquartier];
				$idQuartier = 0;
				$idPays = 0;
				$idVille = 0;
				$stmtHistoriqueAdresse->execute() or die ($mysqliNew->error);
				$idAdresseLien = $idAdresse;
			}
			
			$stmtLienEvenementAdresse->execute() or die ($mysqliNew->error);
		}

		if (!empty($rep->idquartier)) {
//			echo '<p>quartier : '.$rep->iddossier.' - '.$rep->idquartier.'</p>';
			$sql = 'SELECT idAdresse FROM historiqueAdresse WHERE idRue="" AND idSousQuartier="" AND  idQuartier="'.$tabQuartierId[$rep->idquartier].'" AND idVille="" AND idPays="" LIMIT 1';
			$result = $mysqliNew->query($sql) or die ($mysqliNew->error);
			$reponse = $result->fetch_object();
			if (isset($reponse->idAdresse)) {
				$idAdresseLien = $reponse->idAdresse;
			}
			else {
				$idAdresse += 1;
				$nom = '';
				$numero = 0;
				$idRue = 0;
				$idSousQuartier = 0;
				$idQuartier = $tabQuartierId[$rep->idquartier];
				$idPays = 0;
				$idVille = 0;
				$stmtHistoriqueAdresse->execute()  or die ($mysqliNew->error);
				$idAdresseLien = $idAdresse;
			}
			
			$stmtLienEvenementAdresse->execute() or die ($mysqliNew->error);
		}
		
		if (isset($rep->idville)) {
//			echo '<p>ville : '.$rep->iddossier.' - '.$rep->idville.'</p>';
			$sql = 'SELECT idAdresse FROM historiqueAdresse WHERE idRue="" AND idSousQuartier="" AND  idQuartier="" AND idVille="'.$tabVilleId[$rep->idville].'" AND idPays="" LIMIT 1';
			$result = $mysqliNew->query($sql) or die ($mysqliNew->error);
			$reponse = $result->fetch_object();
			if (isset($reponse->idAdresse)) {
				$idAdresseLien = $reponse->idAdresse;
			}
			else {
				$idAdresse += 1;
				$nom    = '';
				$numero = 0;
				$idRue  = 0;
				$idSousQuartier = 0;
				$idQuartier = 0;
				$idPays  = 0;
				$idVille = $tabVilleId[$rep->idville];
				$stmtHistoriqueAdresse->execute() or die ($mysqliNew->error);
				$idAdresseLien = $idAdresse;
			}
			
			$stmtLienEvenementAdresse->execute() or die ($mysqliNew->error);
		}
		*/
	}
	

	////////////////
	////////////////     CRÉATION DES GROUPE D'ADRESSES
	////////////////
	
	$tableauDossiersPere = array();
	$tableauDateDossierPere = array(); // ajout laurent pour recuperer la date de creation du dossier pere
	$sql = 'SELECT iddossier,datedossier FROM dossier WHERE idtypedossier = 1 AND (iddossierpere="" OR iddossierpere IS NULL)';
	$result = $mysqliOld->query($sql) or die($mysqliOld->error);
	$i=0;
	while( $obj = $result->fetch_object())
	{
		$tableauDossiersPere[$i] = $tabEvenementId[$obj->iddossier];
		$tableauDateDossierPere[$tabEvenementId[$obj->iddossier]] = $obj->datedossier;
		$i++;
	}

	
	// création des dossiers de groupe d'adresse

	// Le tableau tabIdDossierGroupeAdresse servira à lier les évènements liés à ce dossier à l'évènement groupe d'adresse
	/*
	
	On veux passer de ça :

	A   -	GA
		   -	Dossier père
		   	    -	Dossier fils
		   	    -	Dossier fils
		   	    -	Dossier fils
		   	    -	Dossier fils

	À :
		
	A   -	GA
		   -	Dossier père
		   -	Dossier fils
		   -	Dossier fils
		   -	Dossier fils
		   -	Dossier fils

	*/
	$tabIdDossierGroupeAdresse = array();
	foreach ($tabEvenementId AS $ancienIdAssocie => $idEvenementAssocie)
	{
		if (!in_array($idEvenementAssocie, $tableauDossiersPere))
		{
			// le dossier est un dossier fils, il ne dois donc pas être connecté directement aux adresses, il les héritera de son dossier père
			$sqlSuppr = 'DELETE FROM _adresseEvenement WHERE idEvenement='.$idEvenementAssocie;
			$mysqliNew->query($sqlSuppr) or die ($mysqliNew->error);
			echo $idEvenementAssocie.' ('.$ancienIdAssocie.')+ ';
		}
		else
		{
			// enregistrement d'un évènement de groupe d'adresse
			// on remplace les ID des evenements déjà enregistrés par l'ID de l'évènement de groupe
			$idEvenement += 1;
			$sql = array();
			$sql[] = 'UPDATE _adresseEvenement SET idEvenement='.$idEvenement.' WHERE idEvenement='.$idEvenementAssocie;
			//$sql[] = 'UPDATE _evenementImage   SET idEvenement='.$idEvenement.' WHERE idEvenement='.$idEvenementAssocie;
			$sql[] = 'INSERT INTO _evenementEvenement (idEvenement, idEvenementAssocie) VALUES ('.$idEvenement.', '.$idEvenementAssocie.')';
			$sql[] = 'INSERT INTO historiqueEvenement (idEvenement, idTypeEvenement,dateCreationEvenement) VALUES ('.$idEvenement.', 11,"'.$tableauDateDossierPere[$idEvenementAssocie].'")'; // 11 = typeEvenement groupeAdresse
			foreach ($sql AS $requete)
			{
				$mysqliNew->query($requete) or die ($mysqliNew->error);
			}

			$tabIdDossierGroupeAdresse[$idEvenementAssocie] = $idEvenement;
		}
	}
	
	
	// création de la table de liaison entre évènements _evenementEvenement
	
	$stmtLienEvenementEvenement = $mysqliNew->prepare("INSERT INTO _evenementEvenement (idEvenement, idEvenementAssocie) VALUES (?, ?)") or die ($mysqliNew->error);
	$stmtLienEvenementEvenement->bind_param('ii', $idEvenementEvenement, $idEvenementEvenementAssocie);
	foreach ($tabEvenementALier AS $value)
	{
		list($idDossier,$idLien)=$value;  // idDossier = idDossierPere    idLien = idDossierFils
		
		// on vérifie si le dossier père n'a pas été enregistré
		if (!isset($tabEvenementId[$idDossier]))
		{
			// le dossier père n'existe pas
			echo '<p>erreur le dossier '.$idDossier.' n\'existe pas</p>';
		}
		else if (!isset($tabIdDossierGroupeAdresse[$tabEvenementId[$idDossier]]))
		{
			echo '<p>erreur le dossier '.$idDossier.' n\'a pas de dossier de groupe d\'adresse</p>';
		}
		else
		{
			echo '<p>nouveau idDossierFils = '.$tabEvenementId[$idLien].' -> nouveau idDossierPere = '.$tabEvenementId[$idDossier].' -> idDossierGroupeAdresse = '.$tabIdDossierGroupeAdresse[$tabEvenementId[$idDossier]].'</p>';
			// le dossier père existe et on peu faire la liaison normalement
			$idEvenementEvenement = $tabIdDossierGroupeAdresse[$tabEvenementId[$idDossier]];
			$idEvenementEvenementAssocie = $tabEvenementId[$idLien];
			$stmtLienEvenementEvenement->execute() or die ($mysqliNew->error);
		}
	}
	
	// Libère de résultat
	$resOld->close();
}

echo 'ok';


//**************

//	IMAGES

//**************
flush();

echo '<h2>Images</h2>';

echo '<h3>Remplacement des espaces</h3>';

$mysqliOld->query("UPDATE image SET urlimage=REPLACE(urlimage, ' ', '') WHERE urlimage LIKE '% %'");
$mysqliOld->query("UPDATE image SET urlimage=REPLACE(urlimage, '\'', '') WHERE urlimage LIKE '%\'%'");
$mysqliOld->query("UPDATE image SET urlimage=REPLACE(urlimage, ',', '') WHERE urlimage LIKE '%,%'");

//$cheminImagesArchiv1 = '/home/laurent/public_html/archilaurent/photos/originaux/';

$cheminImagesArchiv1 = '/home/pia/archi/photos/originaux/';


echo 'ok';

$tabImageId= array();
$tabImage= array();
if ($resOld = $mysqliOld->query("SELECT i.iddossier, i.idimage, i.urlimage, i.libelleimage, i.dateimage, d.datedossier FROM image i
				LEFT JOIN dossier d ON d.iddossier=i.iddossier WHERE (urlimage LIKE '%jpg' OR urlimage LIKE '%png') AND d.idtypedossier=1 ")) {
	echo "sleep";sleep(2);echo "finsleep<br>";	
	$stmt  = $mysqliNew->prepare("INSERT INTO historiqueImage (idHistoriqueImage, idImage, nom, dateUpload, dateCliche, description, idUtilisateur)  
	                        VALUES ('', ?, ?, ?, ?, '', ?)");
	$stmt->bind_param("isssi", $idImage, $nom, $dateUpload, $dateCliche, $idUtilisateurFabien );
	
	$stmtLienEvenementImage = $mysqliNew->prepare("INSERT INTO _evenementImage (idEvenement, idImage) VALUES (?, ?)");
	$stmtLienEvenementImage->bind_param('ii', $idEvenement, $idImage);
	
	$mysqliNew->query('TRUNCATE TABLE `historiqueImage`');
	$mysqliNew->query('TRUNCATE TABLE `_evenementImage`');
	$idImage = 0;
	while ($rep = $resOld->fetch_object()) {
		$nom   = $mysqliNew->escape_string(nettoyeChaine($rep->libelleimage));
		
		$dateCliche = '0000-00-00';
		if (preg_match_all('#[0-9]{8}#', $rep->urlimage, $match))
		{
			// il peu y avoir plus d'une chaine de 8 caractères dans le nom de l'image
			foreach ($match[0] AS $val)
			{
				// si la chaine trouvée est une date
				if (preg_match('#(199|200)#', $val))
				{
					// si la date est à l'endroit
					if ( substr($val, 0, 3) == '200' OR substr($val, 0, 3) == '199')
					{
						$annee = substr($val, 0, 4);
						$mois  = substr($val, 4, 2);
						$jour  = substr($val, 6, 2);
					}
					// si la date est à l'envers
					else
					{
						$annee = substr($val, 4, 4);
						$mois  = substr($val, 2, 2);
						$jour  = substr($val, 0, 2);
					}
					$dateCliche = $annee.'-'.$mois.'-'.$jour;
				}
			}
		}
		$urlImage = nettoyeChaine($rep->urlimage);
		
		// si l'image n'existe pas alors on fait notre enregistrement
		// ET si le dossier est bien migré
		if (!isset($tabImage[nettoyeChaine($rep->urlimage)]))
		{
			if (!isset($tabEvenementId[$rep->iddossier]))
				echo '<p>Erreur : '.$rep->iddossier.'</p>';
			else
			{
				$idImage += 1;
				//echo '<p>Image '.nettoyeChaine($rep->urlimage).' - '.$idImage.' : ';
				$tabImageId[$rep->idimage] = $idImage;
				$tabImage[nettoyeChaine($rep->urlimage)] = $idImage;
				
				
				$dossier = $cheminImagesArchiv1.$rep->iddossier.'/';
				//$nomFichierOld = iconv("ISO-8859-1", "UTF-8", $dossier.$rep->urlimage);
				$nomFichierOld = $dossier.$rep->urlimage;
				$nomFichier = $dossier.'temp.jpg';
				
				$reponseCommande1 = '';
				$reponseCommande2 = '';
				if (!is_file($nomFichierOld))
				{
					$reponseCommandes = array();
					exec("rename 's/ *//g' ".$dossier."*", $reponseCommandes);
					exec("rename \"s/'//g\" ".$dossier."*", $reponseCommandes);
					exec("rename 's/,//g' ".$dossier."*", $reponseCommandes);
					if (count($reponseCommandes) > 0)
					{
						echo '<pre>';
						print_r($reponseCommandes);
						echo '</pre>';
					}
				}

				if (!is_file($nomFichierOld))
				{
					echo '<h1>Erreur</h1>';
					echo '<p>';
					echo "rename 's/ *//g' ".$dossier."*";
					echo "rename 's/\'//g' ".$dossier."*";
					echo "rename 's/ ,//g' ".$dossier."*";
					echo '</p>';
					$nePasSauvegarder = true;
					echo '<p>Le fichier '.$nomFichierOld.' n\'existe pas !</p>';
				}
				else
				{
					$nePasSauvegarder = false;
					
					//
					//  ENREGISTREMENT de l'historiqueImage
					//
					$dateUpload = $rep->datedossier;
					$stmt->execute() or die($mysqliNew->error);
					$idHistoriqueImage = $mysqliNew->insert_id;
					$evementIdImage = $idImage;
					
					//
					//  copie de l'ancien fichier, 
					//  suppression des mauvais noms du dossier (espaces et apostrophes)
					//
					
					$reponseCommandes = array();
					// laurent : pas de copie pour les test , les images sont deja sur le serveur 
					exec('cp '.escapeshellcmd($nomFichierOld).' '.$nomFichier, $reponseCommandes);
					echo "cp ".escapeshellcmd($nomFichierOld).' '.$nomFichier;
					echo "sleep copy";sleep(2);echo "finsleep copy<br>";
					if (count($reponseCommandes) > 0) {
						echo '<pre>';
						print_r($reponseCommandes);
						echo '</pre>';
						echo 'cp '.escapeshellcmd($nomFichierOld).' '.$nomFichier;
						echo '<br />';
						//echo '<br />C1 : '.$reponseCommande1.' <br />C2 : '.$reponseCommande2.'<br />';
					}
					
					$i = new archiImage();
					
					$typeFichier = pia_substr(strtolower($nomFichierOld),-3);
					
		
					
					if(!is_dir($i->cheminPhysiqueImagesOriginaux.$dateUpload)) {
						mkdir($i->cheminPhysiqueImagesOriginaux.$dateUpload)       or die('erreur création : '.$i->cheminPhysiqueImagesOriginaux.$dateUpload);
						chmod($i->cheminPhysiqueImagesOriginaux.$dateUpload, 0777) or die('erreur chmod : '.$i->cheminPhysiqueImagesOriginaux.$dateUpload);
					}
					if(!is_dir($i->cheminPhysiqueImagesMini.$dateUpload)) {
						mkdir($i->cheminPhysiqueImagesMini. $dateUpload)       or die('erreur création : '.$i->cheminPhysiqueImagesMini.$dateUpload);
						chmod($i->cheminPhysiqueImagesMini. $dateUpload, 0777) or die('erreur chmod : '.$i->cheminPhysiqueImagesMini.$dateUpload);
					}
					if(!is_dir($i->cheminPhysiqueImagesMoyen.$dateUpload)) {
						mkdir($i->cheminPhysiqueImagesMoyen.$dateUpload)       or die('erreur création : '.$i->cheminPhysiqueImagesMoyen.$dateUpload);
						chmod($i->cheminPhysiqueImagesMoyen.$dateUpload, 0777) or die('erreur chmod : '.$i->cheminPhysiqueImagesMoyen.$dateUpload);
					}
					if(!is_dir($i->cheminPhysiqueImagesGrand.$dateUpload)) {
						mkdir($i->cheminPhysiqueImagesGrand.$dateUpload)       or die('erreur création : '.$i->cheminPhysiqueImagesGrand.$dateUpload);
						chmod($i->cheminPhysiqueImagesGrand.$dateUpload, 0777) or die('erreur chmod : '.$i->cheminPhysiqueImagesGrand.$dateUpload);
					}
					
			
					$i->redimension( $nomFichier, $typeFichier, $i->cheminPhysiqueImagesOriginaux.$dateUpload.'/'.$idHistoriqueImage.".jpg",0);
					echo 'ok|';echo "sleep"; sleep(1);echo "finsleep<br>";
					$i->redimension( $nomFichier, $typeFichier, $i->cheminPhysiqueImagesMini.$dateUpload.'/'.$idHistoriqueImage.".jpg",80);
					echo 'ok|';echo "sleep";sleep(1);echo "finsleep<br>";
					$i->redimension( $nomFichier, $typeFichier, $i->cheminPhysiqueImagesMoyen.$dateUpload.'/'.$idHistoriqueImage.".jpg",200);
					echo 'ok|';echo "speep";sleep(1);echo "finsleep<br>";
					$i->redimension( $nomFichier, $typeFichier, $i->cheminPhysiqueImagesGrand.$dateUpload.'/'.$idHistoriqueImage.".jpg",500);
					
					echo 'ok</p>';
					passthru('rm '.$nomFichier, $reponseCommande3);
					
				}
			}

		}
		// sinon, on enregistre dans le tableau de correspondance des images l'idImage de l'enregistrement existant déjà
		else {
			$nePasSauvegarder = false;
			$evementIdImage = $tabImage[nettoyeChaine($rep->urlimage)];
			$tabImageId[$rep->idimage] = $tabImage[nettoyeChaine($rep->urlimage)];
		}

		// on s'occupe de la table de liaison _evenementImage
		if ($nePasSauvegarder == false)
		{	
			$idEvenement = $tabEvenementId[$rep->iddossier];
			$stmtLienEvenementImage->execute() or die($mysqliNew->error);
		}
		flush();
	}
	$resOld->close();
}

echo 'ok';

// Fermeture de la connexion 
$mysqliNew->close();
$mysqliOld->close();

// fonction realisant la separation entre le numero et son indicatif : 3bis => 3  et bis
function splitIndicatifFromNumero($num='',$mysqli)
{
	echo "splitIndicatifFromNumero<br>";
	echo $num."<br>";
	$numero	= 0;
	$idIndicatif = 0;
	$continue = true;
	for($i=0 ; $i<pia_strlen($num) && $continue ; $i++)
	{
		$caractereCourant = pia_substr($num , $i , 1);
		if(!is_numeric($caractereCourant))
		{
			$continue = false;
		}
	}
	
	if($continue == true)
	{
		// aucun caractere non numerique n'a ete trouvé
		$numero = $num;
	}
	else
	{
		$reqIndicatif = $mysqli->prepare("select idIndicatif from indicatif where nom = ?");
		
		$numero = pia_substr($num , 0, $i-1);
		$longueurRestante = pia_strlen($num) - ($i-1);
		$indicatif = trim(pia_substr($num ,$i-1 , $longueurRestante));
		// requete pour trouver l'idIndicatif
		//$reqIndicatif = "select * from indicatif where nom = '".$indicatif."'";
		
		$reqIndicatif->bind_param('s', $indicatif ) or die($mysqli->error);
		$reqIndicatif->execute() or die($mysqli->error);
		$reqIndicatif->bind_result($idIndicatif);
		$fetchIndicatif = $reqIndicatif->fetch();
	}
	

	return array('numero'=>$numero,'idIndicatif'=>$idIndicatif);
}


?>
