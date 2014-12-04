CREATE TABLE evenements as (
	SELECT distinct he1.idEvenement ,
	he1.idTypeStructure,
	he1.idTypeEvenement,
	he1.idUtilisateur,
	he1.idSource,
	he1.idEvenementRecuperationTitre,
	he1.idImagePrincipale,
	he1.titre,
	he1.description,
	he1.dateDebut,
	he1.isDateDebutEnviron,
	he1.dateFin,
	he1.dateCreationEvenement, 
	he1.nbEtages,
	he1.ISMH,
	he1.MH,
	he1.numeroArchive,
	he1.parent
	FROM `historiqueEvenement` he1, historiqueEvenement he2
	WHERE NOT EXISTS
	(
	    select ee.idEvenement 
	    from _evenementEvenement ee
	    where ee.idEvenement = he1.idEvenement
	)
	AND he1.idEvenement = he2.idEvenement
	GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
	HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)

);

UPDATE `evenements` SET `idEvenement` = '1000000' WHERE `evenements`.`idEvenement` =0;
ALTER TABLE `evenements` CHANGE `idEvenement` `idEvenement` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
UPDATE `evenements` SET `idEvenement` = '0' WHERE `evenements`.`idEvenement` =1000000;






delimiter //
CREATE TRIGGER trig_historique_evenement_update
BEFORE UPDATE ON evenements FOR EACH ROW
  BEGIN
    SET NEW.dateCreationEvenement = NOW();
    INSERT INTO historiqueEvenement
      ( idEvenement ,
	idTypeStructure,
	idTypeEvenement,
	idUtilisateur,
	idSource,
	idEvenementRecuperationTitre,
	idImagePrincipale,
	titre,
	description,
	dateDebut,
	isDateDebutEnviron,
	dateFin,
	dateCreationEvenement, 
	nbEtages,
	ISMH,
	MH,
	numeroArchive,
	parent)
    VALUES
      (
      
	OLD.idEvenement ,
	OLD.idTypeStructure,
	OLD.idTypeEvenement,
	OLD.idUtilisateur,
	OLD.idSource,
	OLD.idEvenementRecuperationTitre,
	OLD.idImagePrincipale,
	OLD.titre,
	OLD.description,
	OLD.dateDebut,
	OLD.isDateDebutEnviron,
	OLD.dateFin,
	OLD.dateCreationEvenement, 
	OLD.nbEtages,
	OLD.ISMH,
	OLD.MH,
	OLD.numeroArchive,
	OLD.parent
      );
  END;
//
delimiter ;


delimiter //
CREATE TRIGGER trig_historique_evenement_insert
BEFORE INSERT ON evenements FOR EACH ROW
  BEGIN
    SET NEW.dateCreationEvenement = NOW();
    INSERT INTO historiqueEvenement
      ( idEvenement ,
	idTypeStructure,
	idTypeEvenement,
	idUtilisateur,
	idSource,
	idEvenementRecuperationTitre,
	idImagePrincipale,
	titre,
	description,
	dateDebut,
	isDateDebutEnviron,
	dateFin,
	dateCreationEvenement, 
	nbEtages,
	ISMH,
	MH,
	numeroArchive,
	parent)
    VALUES
      (
      
	NEW.idEvenement ,
	NEW.idTypeStructure,
	NEW.idTypeEvenement,
	NEW.idUtilisateur,
	NEW.idSource,
	NEW.idEvenementRecuperationTitre,
	NEW.idImagePrincipale,
	NEW.titre,
	NEW.description,
	NEW.dateDebut,
	NEW.isDateDebutEnviron,
	NEW.dateFin,
	NEW.dateCreationEvenement, 
	NEW.nbEtages,
	NEW.ISMH,
	NEW.MH,
	NEW.numeroArchive,
	NEW.parent
      );
  END;
//


delimiter ;