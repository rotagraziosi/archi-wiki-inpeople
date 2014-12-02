
-- Author : Antoine Rota Graziosi / InPeople
--
--
-- Unset foreign key checking because we are messing with it
-- Need to be reset to 1 at the end
-- Maybe needed to set and unset at each UPDATE call whereas at start and end of this process script
--
-- Resetting process is done in cascade, using fields linked from other tables
-- This modification goes with PHP modification done on oct. 16th 2014 which is setting mutliple ids
-- (idSousQuartier, idQuartier, idVille, idPays) to the correct value, depending on idRue value
-- This allow more flexible data access and might be avoiding useless LEFT join in certain case

SET FOREIGN_KEY_CHECKS = 0;


-- Resetting idSousQuartier using "idRue" and its relation with "idsousQuartier" in "rue" table
UPDATE historiqueAdresse
SET idSousQuartier = (SELECT rue.idSousQuartier
            FROM rue
            WHERE rue.idRue = historiqueAdresse.idRue)
WHERE EXISTS (SELECT rue.idRue
              FROM rue
              WHERE rue.idRue = historiqueAdresse.idRue);
;


-- Resetting idQuartier
UPDATE historiqueAdresse
SET idQuartier = (SELECT sousQuartier.idQuartier
            FROM sousQuartier
            WHERE sousQuartier.idSousQuartier = historiqueAdresse.idSousQuartier)
WHERE EXISTS (SELECT sousQuartier.idSousQuartier
              FROM sousQuartier
              WHERE sousQuartier.idSousQuartier = historiqueAdresse.idSousQuartier);
;


-- Resetting idVille
UPDATE historiqueAdresse
SET idVille = (SELECT quartier.idVille
            FROM quartier
            WHERE quartier.idQuartier = historiqueAdresse.idQuartier)
WHERE EXISTS (SELECT quartier.idVille
              FROM quartier
              WHERE quartier.idQuartier = historiqueAdresse.idQuartier);
;


--Resetting idPays
UPDATE historiqueAdresse
SET idPays = (SELECT ville.idPays
            FROM ville
            WHERE ville.idVille = historiqueAdresse.idVille)
WHERE EXISTS (SELECT ville.idPays
              FROM ville
              WHERE ville.idVille = historiqueAdresse.idVille);
;
SET FOREIGN_KEY_CHECKS = 1;

