-- Author : Antoine Rota Graziosi
-- Modify all the integrity constraint after the evenements table add 


-- Commentaires
ALTER TABLE `commentaires` DROP FOREIGN KEY `commentaires_ibfk_2` ;

ALTER TABLE `commentaires` ADD CONSTRAINT `commentaires_ibfk_3` FOREIGN KEY ( `idEvenementGroupeAdresse` ) REFERENCES `archi_v2`.`evenements` (
`idEvenement`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;


-- Positions evenements
ALTER TABLE `positionsEvenements` DROP FOREIGN KEY `positionsEvenements_ibfk_1` ;

ALTER TABLE `positionsEvenements` ADD CONSTRAINT `positionsEvenements_ibfk_2` FOREIGN KEY ( `idEvenement` ) REFERENCES `archi_v2`.`evenements` (
`idEvenement`
) ON DELETE CASCADE ON UPDATE CASCADE ;


-- EvenementAdresseLiee
ALTER TABLE `_evenementAdresseLiee` DROP FOREIGN KEY `_evenementAdresseLiee_ibfk_3` ;

ALTER TABLE `_evenementAdresseLiee` ADD CONSTRAINT `_evenementAdresseLiee_ibfk_5` FOREIGN KEY ( `idEvenement` ) REFERENCES `archi_v2`.`evenements` (
`idEvenement`
) ON DELETE CASCADE ON UPDATE CASCADE ;


ALTER TABLE `_evenementImage` DROP FOREIGN KEY `_evenementImage_ibfk_3` ;

ALTER TABLE `_evenementImage` ADD CONSTRAINT `_evenementImage_ibfk_3` FOREIGN KEY ( `idEvenement` ) REFERENCES `archi_v2`.`evenements` (
`idEvenement`
) ON DELETE CASCADE ON UPDATE CASCADE ;
