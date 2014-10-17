CREATE TABLE IF NOT EXISTS `commentairesEvenement` (
	`idcommentairesEvenement` int( 11 ) NOT NULL AUTO_INCREMENT ,
	`idHistoriqueEvenement` int( 10 ) unsigned NOT NULL ,
	`commentaire` longtext NOT NULL ,
	`date` datetime NOT NULL ,
	`idUtilisateur` int( 10 ) unsigned NOT NULL DEFAULT '0',
	`CommentaireValide` tinyint( 4 ) NOT NULL DEFAULT '0',
	PRIMARY KEY ( `idcommentairesEvenement` ) ,
	KEY `idHistoriqueEvenement` ( `idHistoriqueEvenement` ) ,
	KEY `idUtilisateur` ( `idUtilisateur` )
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT =6162;
ALTER TABLE `commentairesEvenement` ADD CONSTRAINT `commentairesEvenement_ibfk_1` FOREIGN KEY ( `idUtilisateur` ) REFERENCES `utilisateur` ( `idUtilisateur` ) ,
ADD CONSTRAINT `commentairesEvenement_ibfk_2` FOREIGN KEY ( `idHistoriqueEvenement` ) REFERENCES `historiqueEvenement` ( `idHistoriqueEvenement` ) ;






-- This one works
CREATE TABLE IF NOT EXISTS `archi_v2`.`commentairesEvenement` (
  `idCommentairesEvenement` INT NOT NULL,
  `commentaire` VARCHAR(45) NULL,
  `date` VARCHAR(45) NULL,
  `CommentaireValide` VARCHAR(45) NULL,
  `idHistoriqueEvenement` INT(10) UNSIGNED NOT NULL,
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`idcommentairesEvenement`),
  INDEX `fk_commentairesEvenement_historiqueEvenement1_idx` (`idHistoriqueEvenement` ASC),
  INDEX `fk_commentairesEvenement_utilisateur1_idx` (`idUtilisateur` ASC)
)
ALTER TABLE `commentairesEvenement` ADD CONSTRAINT `commentairesEvenement_ibfk_1` FOREIGN KEY ( `idUtilisateur` ) REFERENCES `utilisateur` ( `idUtilisateur` ) ,
ADD CONSTRAINT `commentairesEvenement_ibfk_2` FOREIGN KEY ( `idHistoriqueEvenement` ) REFERENCES `historiqueEvenement` ( `idHistoriqueEvenement` ) ;
