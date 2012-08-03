-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 03 Août 2012 à 19:39
-- Version du serveur: 5.1.63
-- Version de PHP: 5.3.3-7+squeeze13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `ARCHI_V2`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

CREATE TABLE IF NOT EXISTS `actualites` (
  `idActualite` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `sousTitre` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `photoIllustration` varchar(200) NOT NULL,
  `texte` longtext NOT NULL,
  `urlFichier` varchar(255) NOT NULL,
  `fichierPdf` varchar(100) NOT NULL,
  `desactive` tinyint(1) NOT NULL DEFAULT '0',
  `texteMailHebdomadaire` longtext NOT NULL,
  `envoiMailHebdomadaire` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idActualite`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE IF NOT EXISTS `commentaires` (
  `idCommentaire` int(11) NOT NULL AUTO_INCREMENT,
  `idEvenementGroupeAdresse` int(10) unsigned NOT NULL,
  `pseudo` varchar(150) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prenom` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `commentaire` longtext NOT NULL,
  `date` datetime NOT NULL,
  `idUtilisateur` int(10) unsigned NOT NULL DEFAULT '0',
  `CommentaireValide` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idCommentaire`),
  KEY `idEvenementGroupeAdresse` (`idEvenementGroupeAdresse`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1949 ;

-- --------------------------------------------------------

--
-- Structure de la table `complementNewsLetterHebdo`
--

CREATE TABLE IF NOT EXISTS `complementNewsLetterHebdo` (
  `idComplement` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00' COMMENT 'suivant la date , on envoit le complement en meme temps que la news letter precedent cette date',
  `texte` longtext NOT NULL,
  PRIMARY KEY (`idComplement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `connexionsUtilisateurs`
--

CREATE TABLE IF NOT EXISTS `connexionsUtilisateurs` (
  `idConnexion` int(11) NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int(10) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idConnexion`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19533 ;

-- --------------------------------------------------------

--
-- Structure de la table `courantArchitectural`
--

CREATE TABLE IF NOT EXISTS `courantArchitectural` (
  `idCourantArchitectural` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`idCourantArchitectural`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

CREATE TABLE IF NOT EXISTS `droits` (
  `idDroit` int(11) NOT NULL AUTO_INCREMENT,
  `idProfil` int(11) NOT NULL DEFAULT '0',
  `idElementSite` int(11) NOT NULL DEFAULT '0',
  `acces` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idDroit`),
  KEY `idProfil` (`idProfil`),
  KEY `idElementSite` (`idElementSite`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=896 ;

-- --------------------------------------------------------

--
-- Structure de la table `droitsElementsSite`
--

CREATE TABLE IF NOT EXISTS `droitsElementsSite` (
  `idElementSite` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) NOT NULL,
  PRIMARY KEY (`idElementSite`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Structure de la table `droitsProfils`
--

CREATE TABLE IF NOT EXISTS `droitsProfils` (
  `idProfil` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(150) NOT NULL,
  PRIMARY KEY (`idProfil`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `etapesParcoursArt`
--

CREATE TABLE IF NOT EXISTS `etapesParcoursArt` (
  `idEtape` int(11) NOT NULL AUTO_INCREMENT,
  `idParcours` int(11) NOT NULL DEFAULT '0',
  `idEvenementGroupeAdresse` int(10) unsigned NOT NULL,
  `commentaireEtape` longtext NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idEtape`),
  KEY `idParcours` (`idParcours`),
  KEY `idEvenementGroupeAdresse` (`idEvenementGroupeAdresse`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=306 ;

-- --------------------------------------------------------

--
-- Structure de la table `etapesParcoursArt20120702`
--

CREATE TABLE IF NOT EXISTS `etapesParcoursArt20120702` (
  `idEtape` int(11) NOT NULL AUTO_INCREMENT,
  `idParcours` int(11) NOT NULL DEFAULT '0',
  `idEvenementGroupeAdresse` int(10) unsigned NOT NULL,
  `commentaireEtape` longtext NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idEtape`),
  KEY `idParcours` (`idParcours`),
  KEY `idEvenementGroupeAdresse` (`idEvenementGroupeAdresse`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90 ;

-- --------------------------------------------------------

--
-- Structure de la table `gestionFichiersCache`
--

CREATE TABLE IF NOT EXISTS `gestionFichiersCache` (
  `idCache` int(11) NOT NULL AUTO_INCREMENT,
  `className` varchar(255) CHARACTER SET utf8 NOT NULL,
  `methodName` varchar(255) CHARACTER SET utf8 NOT NULL,
  `params` text CHARACTER SET utf8 NOT NULL,
  `fileName` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`idCache`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=158 ;

-- --------------------------------------------------------

--
-- Structure de la table `historiqueAdresse`
--

CREATE TABLE IF NOT EXISTS `historiqueAdresse` (
  `idHistoriqueAdresse` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idAdresse` int(10) unsigned NOT NULL,
  `idRue` int(10) unsigned NOT NULL DEFAULT '0',
  `idSousQuartier` int(10) unsigned NOT NULL DEFAULT '0',
  `idQuartier` int(10) unsigned NOT NULL DEFAULT '0',
  `idVille` int(10) unsigned NOT NULL DEFAULT '1',
  `idPays` int(10) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `nom` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `numero` smallint(5) unsigned NOT NULL DEFAULT '0',
  `idIndicatif` int(11) NOT NULL DEFAULT '0',
  `idUtilisateur` int(11) NOT NULL DEFAULT '0',
  `latitude` varchar(20) NOT NULL DEFAULT '0',
  `longitude` varchar(20) NOT NULL DEFAULT '0',
  `coordonneesVerrouillees` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idHistoriqueAdresse`),
  KEY `idRue` (`idRue`),
  KEY `numero` (`numero`),
  KEY `idx_STRAS` (`idHistoriqueAdresse`,`idVille`),
  KEY `idx_STRAS_Rue` (`idRue`,`idVille`),
  KEY `idAdresse` (`idAdresse`),
  KEY `idSousQuartier` (`idSousQuartier`),
  KEY `idQuartier` (`idQuartier`),
  KEY `idVille` (`idVille`),
  KEY `idPays` (`idPays`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7539 ;

-- --------------------------------------------------------

--
-- Structure de la table `historiqueEvenement`
--

CREATE TABLE IF NOT EXISTS `historiqueEvenement` (
  `idEvenement` int(10) unsigned NOT NULL,
  `idTypeStructure` int(10) unsigned NOT NULL,
  `idTypeEvenement` int(10) unsigned NOT NULL,
  `idHistoriqueEvenement` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int(10) unsigned NOT NULL,
  `idSource` smallint(5) unsigned NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `dateDebut` date NOT NULL,
  `isDateDebutEnviron` tinyint(1) NOT NULL DEFAULT '0',
  `dateFin` date NOT NULL,
  `dateCreationEvenement` datetime NOT NULL,
  `nbEtages` int(11) NOT NULL DEFAULT '0',
  `ISMH` tinyint(4) NOT NULL DEFAULT '0',
  `MH` tinyint(4) NOT NULL DEFAULT '0',
  `numeroArchive` varchar(30) NOT NULL,
  `idEvenementRecuperationTitre` int(11) NOT NULL DEFAULT '0',
  `idImagePrincipale` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idHistoriqueEvenement`),
  KEY `idEvenement` (`idEvenement`),
  KEY `idSource` (`idSource`),
  KEY `IDX_Evt` (`idEvenement`,`idHistoriqueEvenement`),
  KEY `idTypeStructure` (`idTypeStructure`),
  KEY `idTypeEvenement` (`idTypeEvenement`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idImagePrincipale` (`idImagePrincipale`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40108 ;

-- --------------------------------------------------------

--
-- Structure de la table `historiqueImage`
--

CREATE TABLE IF NOT EXISTS `historiqueImage` (
  `idHistoriqueImage` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idImage` int(10) unsigned NOT NULL,
  `idUtilisateur` int(10) unsigned NOT NULL,
  `nom` varchar(200) NOT NULL,
  `dateUpload` date NOT NULL,
  `dateCliche` date NOT NULL,
  `isDateClicheEnviron` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `idSource` smallint(5) unsigned NOT NULL,
  `numeroArchive` varchar(30) NOT NULL,
  `licence` int(11) NOT NULL DEFAULT '1',
  `auteur` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idHistoriqueImage`),
  KEY `idImage` (`idImage`),
  KEY `idSource` (`idSource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57898 ;

-- --------------------------------------------------------

--
-- Structure de la table `historiqueNomsRues`
--

CREATE TABLE IF NOT EXISTS `historiqueNomsRues` (
  `idHistoriqueNomRue` int(11) NOT NULL AUTO_INCREMENT,
  `idRue` int(10) unsigned NOT NULL DEFAULT '0',
  `annee` date NOT NULL,
  `prefixe` varchar(100) NOT NULL,
  `nomRue` varchar(255) NOT NULL,
  `commentaire` longtext NOT NULL,
  PRIMARY KEY (`idHistoriqueNomRue`),
  KEY `idRue` (`idRue`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2113 ;

-- --------------------------------------------------------

--
-- Structure de la table `imagesUploadeesPourRegeneration`
--

CREATE TABLE IF NOT EXISTS `imagesUploadeesPourRegeneration` (
  `idImageUploadee` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idImage` int(10) unsigned NOT NULL DEFAULT '0',
  `cheminImageUploadee` varchar(255) NOT NULL DEFAULT '',
  `idHistoriqueImage` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idImageUploadee`),
  KEY `idImage` (`idImage`),
  KEY `idHistoriqueImage` (`idHistoriqueImage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8092 ;

-- --------------------------------------------------------

--
-- Structure de la table `indicatif`
--

CREATE TABLE IF NOT EXISTS `indicatif` (
  `idIndicatif` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(10) NOT NULL,
  PRIMARY KEY (`idIndicatif`),
  KEY `nom` (`nom`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `licences`
--

CREATE TABLE IF NOT EXISTS `licences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8 NOT NULL,
  `link` varchar(2000) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `id` char(23) CHARACTER SET utf8 NOT NULL COMMENT 'ID unique à comparer avec le cookie',
  `user` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Adresse e-mail de l''utilisateur',
  PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Gère les connexions persistentes';

-- --------------------------------------------------------

--
-- Structure de la table `logMails`
--

CREATE TABLE IF NOT EXISTS `logMails` (
  `idMail` int(11) NOT NULL AUTO_INCREMENT,
  `destinataire` varchar(255) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `isDebug` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'ce champ est a 1 si le message est un message en mode debug, donc pas envoyé en fait',
  PRIMARY KEY (`idMail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=147177 ;

-- --------------------------------------------------------

--
-- Structure de la table `mailsEnvoiMailsRegroupes`
--

CREATE TABLE IF NOT EXISTS `mailsEnvoiMailsRegroupes` (
  `idMail` int(11) NOT NULL AUTO_INCREMENT,
  `dateHeure` datetime NOT NULL,
  `idUtilisateur` int(10) unsigned NOT NULL,
  `contenu` longtext NOT NULL,
  `intitule` varchar(255) NOT NULL,
  `idTypeMailRegroupement` int(11) NOT NULL,
  PRIMARY KEY (`idMail`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idTypeMailRegroupement` (`idTypeMailRegroupement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=277618 ;

-- --------------------------------------------------------

--
-- Structure de la table `metier`
--

CREATE TABLE IF NOT EXISTS `metier` (
  `idMetier` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  PRIMARY KEY (`idMetier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `lang` char(5) CHARACTER SET utf8 NOT NULL DEFAULT 'fr_FR',
  `menu` tinyint(1) NOT NULL DEFAULT '0',
  `footer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `parcoursArt`
--

CREATE TABLE IF NOT EXISTS `parcoursArt` (
  `idParcours` int(11) NOT NULL AUTO_INCREMENT,
  `dateAjoutParcours` date NOT NULL DEFAULT '0000-00-00',
  `libelleParcours` varchar(200) NOT NULL,
  `commentaireParcours` longtext NOT NULL,
  `idSource` smallint(5) unsigned NOT NULL DEFAULT '0',
  `isActif` tinyint(1) NOT NULL DEFAULT '0',
  `trace` varchar(2000) NOT NULL COMMENT 'Tracé polyline du parcours',
  `levels` varchar(2000) NOT NULL COMMENT 'Niveaux polyline',
  PRIMARY KEY (`idParcours`),
  KEY `idSource` (`idSource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

CREATE TABLE IF NOT EXISTS `pays` (
  `idPays` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`idPays`),
  KEY `nom` (`nom`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `periodesEnvoiMailsRegroupes`
--

CREATE TABLE IF NOT EXISTS `periodesEnvoiMailsRegroupes` (
  `idPeriode` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(150) NOT NULL,
  PRIMARY KEY (`idPeriode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

CREATE TABLE IF NOT EXISTS `personne` (
  `idPersonne` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `idMetier` int(11) NOT NULL DEFAULT '0',
  `dateNaissance` date NOT NULL,
  `dateDeces` date NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`idPersonne`),
  KEY `idMetier` (`idMetier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=855 ;

-- --------------------------------------------------------

--
-- Structure de la table `positionsEvenements`
--

CREATE TABLE IF NOT EXISTS `positionsEvenements` (
  `idPosition` int(11) NOT NULL AUTO_INCREMENT,
  `idEvenementGroupeAdresse` int(11) NOT NULL DEFAULT '0',
  `idEvenement` int(10) unsigned NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idPosition`),
  KEY `idEvenement` (`idEvenement`),
  KEY `idEvenementGroupeAdresse` (`idEvenementGroupeAdresse`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31712 ;

-- --------------------------------------------------------

--
-- Structure de la table `quartier`
--

CREATE TABLE IF NOT EXISTS `quartier` (
  `idQuartier` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idVille` int(10) unsigned NOT NULL,
  `nom` varchar(50) NOT NULL,
  `codepostal` varchar(6) NOT NULL,
  PRIMARY KEY (`idQuartier`),
  KEY `nom` (`nom`),
  KEY `idVille` (`idVille`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Structure de la table `rue`
--

CREATE TABLE IF NOT EXISTS `rue` (
  `idRue` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idSousQuartier` int(10) unsigned NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prefixe` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idRue`),
  KEY `nom` (`nom`),
  KEY `prefixe` (`prefixe`),
  KEY `idSousQuartier` (`idSousQuartier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1448 ;

-- --------------------------------------------------------

--
-- Structure de la table `sondagesPropositions`
--

CREATE TABLE IF NOT EXISTS `sondagesPropositions` (
  `idProposition` int(11) NOT NULL AUTO_INCREMENT,
  `idSondage` int(11) NOT NULL,
  `libelleProposition` varchar(255) NOT NULL,
  PRIMARY KEY (`idProposition`),
  KEY `idSondage` (`idSondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `sondagesResultats`
--

CREATE TABLE IF NOT EXISTS `sondagesResultats` (
  `idResultat` int(11) NOT NULL AUTO_INCREMENT,
  `idSondage` int(11) NOT NULL,
  `date` date NOT NULL,
  `ip` varchar(20) NOT NULL,
  `idProposition` int(11) NOT NULL,
  PRIMARY KEY (`idResultat`),
  KEY `idProposition` (`idProposition`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Structure de la table `source`
--

CREATE TABLE IF NOT EXISTS `source` (
  `idSource` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idTypeSource` tinyint(3) unsigned NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`idSource`),
  KEY `idTypeSource` (`idTypeSource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=310 ;

-- --------------------------------------------------------

--
-- Structure de la table `sousQuartier`
--

CREATE TABLE IF NOT EXISTS `sousQuartier` (
  `idSousQuartier` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idQuartier` int(10) unsigned NOT NULL,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`idSousQuartier`),
  KEY `nom` (`nom`),
  KEY `idQuartier` (`idQuartier`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

-- --------------------------------------------------------

--
-- Structure de la table `typeEvenement`
--

CREATE TABLE IF NOT EXISTS `typeEvenement` (
  `idTypeEvenement` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `groupe` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : ''Culturel'', 2: ''Travaux'', 3: ''Caché''',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idTypeEvenement`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Structure de la table `typesMailsEnvoiMailsRegroupes`
--

CREATE TABLE IF NOT EXISTS `typesMailsEnvoiMailsRegroupes` (
  `idTypeMail` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  PRIMARY KEY (`idTypeMail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Structure de la table `typeSource`
--

CREATE TABLE IF NOT EXISTS `typeSource` (
  `idTypeSource` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`idTypeSource`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Structure de la table `typeStructure`
--

CREATE TABLE IF NOT EXISTS `typeStructure` (
  `idTypeStructure` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`idTypeStructure`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idUtilisateur` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `motDePasse` varchar(255) NOT NULL,
  `urlSiteWeb` varchar(255) NOT NULL,
  `idProfil` int(11) NOT NULL DEFAULT '0',
  `alerteMail` tinyint(1) NOT NULL DEFAULT '0',
  `alerteAdresses` tinyint(4) NOT NULL DEFAULT '0',
  `alerteCommentaires` tinyint(4) NOT NULL DEFAULT '0',
  `displayProfilContactForm` tinyint(1) NOT NULL DEFAULT '1',
  `displayNumeroArchiveFieldInSaisieEvenement` tinyint(1) NOT NULL DEFAULT '0',
  `displayDateFinFieldInSaisieEvenement` tinyint(1) NOT NULL DEFAULT '0',
  `idPeriodeEnvoiMailsRegroupes` int(11) NOT NULL DEFAULT '0',
  `idVilleFavoris` int(10) unsigned NOT NULL DEFAULT '0',
  `compteActif` tinyint(2) NOT NULL DEFAULT '0',
  `idActivateur` int(11) NOT NULL DEFAULT '0',
  `compteBanni` tinyint(1) NOT NULL DEFAULT '0',
  `dateCreation` datetime NOT NULL,
  PRIMARY KEY (`idUtilisateur`),
  KEY `idPeriodeEnvoiMailsRegroupes` (`idPeriodeEnvoiMailsRegroupes`),
  KEY `idVilleFavoris` (`idVilleFavoris`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1414 ;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurModerateurVille`
--

CREATE TABLE IF NOT EXISTS `utilisateurModerateurVille` (
  `idModeration` int(11) NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int(10) unsigned NOT NULL DEFAULT '0',
  `idVille` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idModeration`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idVille` (`idVille`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;

-- --------------------------------------------------------

--
-- Structure de la table `verrouTable`
--

CREATE TABLE IF NOT EXISTS `verrouTable` (
  `verrouName` varchar(50) NOT NULL,
  `timeOut` datetime NOT NULL,
  `idUtilisateur` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `verrouName` (`verrouName`,`idUtilisateur`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `verticesParcours`
--

CREATE TABLE IF NOT EXISTS `verticesParcours` (
  `idVertex` int(11) NOT NULL AUTO_INCREMENT,
  `idParcours` int(11) NOT NULL DEFAULT '0',
  `idEtape` int(11) NOT NULL DEFAULT '0',
  `longitude` varchar(30) NOT NULL,
  `latitude` varchar(30) NOT NULL,
  `position` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idVertex`),
  KEY `idParcours` (`idParcours`),
  KEY `idEtape` (`idEtape`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE IF NOT EXISTS `ville` (
  `idVille` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idPays` int(10) unsigned NOT NULL,
  `nom` varchar(50) NOT NULL,
  `codepostal` varchar(6) NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  PRIMARY KEY (`idVille`),
  KEY `nom` (`nom`),
  KEY `idPays` (`idPays`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Structure de la table `_adresseEvenement`
--

CREATE TABLE IF NOT EXISTS `_adresseEvenement` (
  `idAdresse` int(10) unsigned NOT NULL,
  `idEvenement` int(11) NOT NULL,
  `longitudeGroupeAdresse` varchar(20) NOT NULL DEFAULT '0',
  `latitudeGroupeAdresse` varchar(20) NOT NULL DEFAULT '0',
  KEY `idAdresse` (`idAdresse`),
  KEY `idEvenement` (`idEvenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_adresseImage`
--

CREATE TABLE IF NOT EXISTS `_adresseImage` (
  `idImage` int(10) unsigned NOT NULL,
  `idAdresse` int(10) unsigned NOT NULL,
  `idEvenementGroupeAdresse` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `vueSur` tinyint(1) NOT NULL DEFAULT '0',
  `prisDepuis` tinyint(1) NOT NULL DEFAULT '0',
  `seSitue` int(11) NOT NULL DEFAULT '0',
  `etage` tinyint(4) NOT NULL DEFAULT '0',
  `hauteur` tinyint(4) NOT NULL DEFAULT '0',
  `coordonneesZoneImage` varchar(255) NOT NULL,
  `largeurBaseZoneImage` varchar(5) NOT NULL,
  `longueurBaseZoneImage` varchar(5) NOT NULL,
  KEY `idImage` (`idImage`),
  KEY `idAdresse` (`idAdresse`),
  KEY `idEvenementGroupeAdresse` (`idEvenementGroupeAdresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_adresseImageCopie`
--

CREATE TABLE IF NOT EXISTS `_adresseImageCopie` (
  `idImage` int(10) unsigned NOT NULL,
  `idAdresse` int(10) unsigned NOT NULL,
  `idEvenementGroupeAdresse` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL,
  `vueSur` tinyint(1) NOT NULL DEFAULT '0',
  `prisDepuis` tinyint(1) NOT NULL DEFAULT '0',
  `seSitue` int(11) NOT NULL DEFAULT '0',
  `etage` tinyint(4) NOT NULL DEFAULT '0',
  `hauteur` tinyint(4) NOT NULL DEFAULT '0',
  `coordonneesZoneImage` varchar(255) NOT NULL,
  `largeurBaseZoneImage` varchar(5) NOT NULL,
  `longueurBaseZoneImage` varchar(5) NOT NULL,
  KEY `idImage` (`idImage`),
  KEY `idAdresse` (`idAdresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_evenementAdresseLiee`
--

CREATE TABLE IF NOT EXISTS `_evenementAdresseLiee` (
  `idEvenement` int(10) unsigned NOT NULL DEFAULT '0',
  `idAdresse` int(10) unsigned NOT NULL DEFAULT '0',
  `idEvenementGroupeAdresse` int(11) NOT NULL DEFAULT '0',
  KEY `idEvenement` (`idEvenement`),
  KEY `idAdresse` (`idAdresse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_evenementCourantArchitectural`
--

CREATE TABLE IF NOT EXISTS `_evenementCourantArchitectural` (
  `idCourantArchitectural` int(10) unsigned NOT NULL,
  `idEvenement` int(10) unsigned NOT NULL,
  KEY `idCourantArchitectural` (`idCourantArchitectural`),
  KEY `idEvenement` (`idEvenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_evenementEvenement`
--

CREATE TABLE IF NOT EXISTS `_evenementEvenement` (
  `idEvenement` int(10) unsigned NOT NULL,
  `idEvenementAssocie` int(10) unsigned NOT NULL,
  KEY `idEvenement` (`idEvenement`),
  KEY `idEvenementAssocie` (`idEvenementAssocie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_evenementImage`
--

CREATE TABLE IF NOT EXISTS `_evenementImage` (
  `idEvenement` int(10) unsigned NOT NULL,
  `idImage` int(10) unsigned NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  KEY `idEvenement` (`idEvenement`),
  KEY `idImage` (`idImage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_evenementPersonne`
--

CREATE TABLE IF NOT EXISTS `_evenementPersonne` (
  `idEvenement` int(10) unsigned NOT NULL,
  `idPersonne` int(10) unsigned NOT NULL,
  KEY `idEvenement` (`idEvenement`),
  KEY `idPersonne` (`idPersonne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_personneEvenement`
--

CREATE TABLE IF NOT EXISTS `_personneEvenement` (
  `idPersonne` int(10) unsigned NOT NULL,
  `idEvenement` int(10) unsigned NOT NULL,
  KEY `idAdresse` (`idPersonne`),
  KEY `idEvenement` (`idEvenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `_personneImage`
--

CREATE TABLE IF NOT EXISTS `_personneImage` (
  `idPersonne` int(10) unsigned NOT NULL,
  `idImage` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idPersonne`,`idImage`),
  KEY `idImage` (`idImage`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`idEvenementGroupeAdresse`) REFERENCES `historiqueEvenement` (`idEvenement`);

--
-- Contraintes pour la table `connexionsUtilisateurs`
--
ALTER TABLE `connexionsUtilisateurs`
  ADD CONSTRAINT `connexionsUtilisateurs_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`);

--
-- Contraintes pour la table `droits`
--
ALTER TABLE `droits`
  ADD CONSTRAINT `droits_ibfk_4` FOREIGN KEY (`idElementSite`) REFERENCES `droitsElementsSite` (`idElementSite`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `droits_ibfk_5` FOREIGN KEY (`idProfil`) REFERENCES `droitsProfils` (`idProfil`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `etapesParcoursArt`
--
ALTER TABLE `etapesParcoursArt`
  ADD CONSTRAINT `etapesParcoursArt_ibfk_1` FOREIGN KEY (`idParcours`) REFERENCES `parcoursArt` (`idParcours`),
  ADD CONSTRAINT `etapesParcoursArt_ibfk_2` FOREIGN KEY (`idEvenementGroupeAdresse`) REFERENCES `historiqueEvenement` (`idEvenement`);

--
-- Contraintes pour la table `historiqueAdresse`
--
ALTER TABLE `historiqueAdresse`
  ADD CONSTRAINT `historiqueAdresse_ibfk_1` FOREIGN KEY (`idRue`) REFERENCES `rue` (`idRue`),
  ADD CONSTRAINT `historiqueAdresse_ibfk_2` FOREIGN KEY (`idSousQuartier`) REFERENCES `sousQuartier` (`idSousQuartier`),
  ADD CONSTRAINT `historiqueAdresse_ibfk_3` FOREIGN KEY (`idQuartier`) REFERENCES `quartier` (`idQuartier`),
  ADD CONSTRAINT `historiqueAdresse_ibfk_4` FOREIGN KEY (`idVille`) REFERENCES `ville` (`idVille`),
  ADD CONSTRAINT `historiqueAdresse_ibfk_5` FOREIGN KEY (`idPays`) REFERENCES `pays` (`idPays`);

--
-- Contraintes pour la table `historiqueEvenement`
--
ALTER TABLE `historiqueEvenement`
  ADD CONSTRAINT `historiqueEvenement_ibfk_1` FOREIGN KEY (`idTypeEvenement`) REFERENCES `typeEvenement` (`idTypeEvenement`),
  ADD CONSTRAINT `historiqueEvenement_ibfk_2` FOREIGN KEY (`idSource`) REFERENCES `source` (`idSource`),
  ADD CONSTRAINT `historiqueEvenement_ibfk_3` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `historiqueEvenement_ibfk_4` FOREIGN KEY (`idTypeStructure`) REFERENCES `typeStructure` (`idTypeStructure`),
  ADD CONSTRAINT `historiqueEvenement_ibfk_5` FOREIGN KEY (`idImagePrincipale`) REFERENCES `historiqueImage` (`idImage`);

--
-- Contraintes pour la table `historiqueImage`
--
ALTER TABLE `historiqueImage`
  ADD CONSTRAINT `historiqueImage_ibfk_1` FOREIGN KEY (`idSource`) REFERENCES `source` (`idSource`);

--
-- Contraintes pour la table `historiqueNomsRues`
--
ALTER TABLE `historiqueNomsRues`
  ADD CONSTRAINT `historiqueNomsRues_ibfk_1` FOREIGN KEY (`idRue`) REFERENCES `rue` (`idRue`);

--
-- Contraintes pour la table `imagesUploadeesPourRegeneration`
--
ALTER TABLE `imagesUploadeesPourRegeneration`
  ADD CONSTRAINT `imagesUploadeesPourRegeneration_ibfk_4` FOREIGN KEY (`idHistoriqueImage`) REFERENCES `historiqueImage` (`idHistoriqueImage`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imagesUploadeesPourRegeneration_ibfk_3` FOREIGN KEY (`idImage`) REFERENCES `historiqueImage` (`idImage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `mailsEnvoiMailsRegroupes`
--
ALTER TABLE `mailsEnvoiMailsRegroupes`
  ADD CONSTRAINT `mailsEnvoiMailsRegroupes_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `mailsEnvoiMailsRegroupes_ibfk_2` FOREIGN KEY (`idTypeMailRegroupement`) REFERENCES `typesMailsEnvoiMailsRegroupes` (`idTypeMail`);

--
-- Contraintes pour la table `parcoursArt`
--
ALTER TABLE `parcoursArt`
  ADD CONSTRAINT `parcoursArt_ibfk_1` FOREIGN KEY (`idSource`) REFERENCES `source` (`idSource`);

--
-- Contraintes pour la table `personne`
--
ALTER TABLE `personne`
  ADD CONSTRAINT `personne_ibfk_1` FOREIGN KEY (`idMetier`) REFERENCES `metier` (`idMetier`);

--
-- Contraintes pour la table `positionsEvenements`
--
ALTER TABLE `positionsEvenements`
  ADD CONSTRAINT `positionsEvenements_ibfk_1` FOREIGN KEY (`idEvenement`) REFERENCES `historiqueEvenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `quartier`
--
ALTER TABLE `quartier`
  ADD CONSTRAINT `quartier_ibfk_1` FOREIGN KEY (`idVille`) REFERENCES `ville` (`idVille`);

--
-- Contraintes pour la table `rue`
--
ALTER TABLE `rue`
  ADD CONSTRAINT `rue_ibfk_1` FOREIGN KEY (`idSousQuartier`) REFERENCES `sousQuartier` (`idSousQuartier`);

--
-- Contraintes pour la table `sondagesResultats`
--
ALTER TABLE `sondagesResultats`
  ADD CONSTRAINT `sondagesResultats_ibfk_1` FOREIGN KEY (`idProposition`) REFERENCES `sondagesPropositions` (`idProposition`);

--
-- Contraintes pour la table `source`
--
ALTER TABLE `source`
  ADD CONSTRAINT `source_ibfk_1` FOREIGN KEY (`idTypeSource`) REFERENCES `source` (`idTypeSource`);

--
-- Contraintes pour la table `sousQuartier`
--
ALTER TABLE `sousQuartier`
  ADD CONSTRAINT `sousQuartier_ibfk_1` FOREIGN KEY (`idQuartier`) REFERENCES `quartier` (`idQuartier`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`idVilleFavoris`) REFERENCES `ville` (`idVille`),
  ADD CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`idPeriodeEnvoiMailsRegroupes`) REFERENCES `periodesEnvoiMailsRegroupes` (`idPeriode`);

--
-- Contraintes pour la table `utilisateurModerateurVille`
--
ALTER TABLE `utilisateurModerateurVille`
  ADD CONSTRAINT `utilisateurModerateurVille_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `utilisateurModerateurVille_ibfk_2` FOREIGN KEY (`idVille`) REFERENCES `ville` (`idVille`);

--
-- Contraintes pour la table `verrouTable`
--
ALTER TABLE `verrouTable`
  ADD CONSTRAINT `verrouTable_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`);

--
-- Contraintes pour la table `verticesParcours`
--
ALTER TABLE `verticesParcours`
  ADD CONSTRAINT `verticesParcours_ibfk_1` FOREIGN KEY (`idParcours`) REFERENCES `parcoursArt` (`idParcours`),
  ADD CONSTRAINT `verticesParcours_ibfk_2` FOREIGN KEY (`idEtape`) REFERENCES `etapesParcoursArt` (`idEtape`);

--
-- Contraintes pour la table `ville`
--
ALTER TABLE `ville`
  ADD CONSTRAINT `ville_ibfk_1` FOREIGN KEY (`idPays`) REFERENCES `pays` (`idPays`);

--
-- Contraintes pour la table `_adresseEvenement`
--
ALTER TABLE `_adresseEvenement`
  ADD CONSTRAINT `_adresseEvenement_ibfk_3` FOREIGN KEY (`idAdresse`) REFERENCES `historiqueAdresse` (`idAdresse`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_adresseImage`
--
ALTER TABLE `_adresseImage`
  ADD CONSTRAINT `_adresseImage_ibfk_3` FOREIGN KEY (`idImage`) REFERENCES `historiqueImage` (`idImage`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_adresseImage_ibfk_4` FOREIGN KEY (`idAdresse`) REFERENCES `historiqueAdresse` (`idAdresse`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_adresseImage_ibfk_5` FOREIGN KEY (`idEvenementGroupeAdresse`) REFERENCES `historiqueEvenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_evenementAdresseLiee`
--
ALTER TABLE `_evenementAdresseLiee`
  ADD CONSTRAINT `_evenementAdresseLiee_ibfk_3` FOREIGN KEY (`idEvenement`) REFERENCES `historiqueEvenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_evenementAdresseLiee_ibfk_4` FOREIGN KEY (`idAdresse`) REFERENCES `historiqueAdresse` (`idAdresse`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_evenementCourantArchitectural`
--
ALTER TABLE `_evenementCourantArchitectural`
  ADD CONSTRAINT `_evenementCourantArchitectural_ibfk_3` FOREIGN KEY (`idCourantArchitectural`) REFERENCES `courantArchitectural` (`idCourantArchitectural`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_evenementEvenement`
--
ALTER TABLE `_evenementEvenement`
  ADD CONSTRAINT `_evenementEvenement_ibfk_2` FOREIGN KEY (`idEvenement`) REFERENCES `historiqueEvenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_evenementImage`
--
ALTER TABLE `_evenementImage`
  ADD CONSTRAINT `_evenementImage_ibfk_3` FOREIGN KEY (`idEvenement`) REFERENCES `historiqueEvenement` (`idEvenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_evenementImage_ibfk_4` FOREIGN KEY (`idImage`) REFERENCES `historiqueImage` (`idImage`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_evenementPersonne`
--
ALTER TABLE `_evenementPersonne`
  ADD CONSTRAINT `_evenementPersonne_ibfk_4` FOREIGN KEY (`idPersonne`) REFERENCES `personne` (`idPersonne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `_personneImage`
--
ALTER TABLE `_personneImage`
  ADD CONSTRAINT `_personneImage_ibfk_1` FOREIGN KEY (`idPersonne`) REFERENCES `personne` (`idPersonne`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `_personneImage_ibfk_2` FOREIGN KEY (`idImage`) REFERENCES `historiqueImage` (`idImage`) ON DELETE CASCADE ON UPDATE CASCADE;
