-- adresseEvenementPatcher.sql
-- Refactor links between adresse and evenements, those are linked using _adresseEvenement 
-- and no more dork groupe evenement empty line in historiqueEvenement with _evenementEvenement table
-- Author : Antoine Rota Graziosi


-- Might be the one !
CREATE TABLE _adrEvent as (
SELECT idEvenement, idAdresse
FROM _adresseEvenement
PRIMARY KEY (`idEvenement`,`idAdresse`)
)


INSERT INTO _adrEvent 
SELECT distinct he1.idEvenement as idEvenement, ae.idAdresse as idAdresse, ha.latitude as latitudeGroupeAdresse, ha.longitude as longitudeGroupeAdresse
FROM _adresseEvenement ae
LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
LEFT JOIN historiqueAdresse ha on ha.idAdresse = ae.idAdresse
GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
ORDER BY he1.dateDebut,he1.idHistoriqueEvenement

ALTER TABLE `evenements` ADD PRIMARY KEY(`idEvenement`);


-- Not used, for debug and processing only

/*
CREATE TABLE _adrEvt as(
	SELECT distinct he1.idEvenement as idEvenement, ae.idAdresse as idAdresse, ha.latitude as latitudeGroupeAdresse, ha.longitude as longitudeGroupeAdresse
	FROM _adresseEvenement ae
	LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
	LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
	LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
	LEFT JOIN historiqueAdresse ha on ha.idAdresse = ae.idAdresse
	GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
	HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
	ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
)









SELECT distinct he1.idEvenement as idEvenement, ae.idAdresse as idAdresse, ha.latitude as latitudeGroupeAdresse, ha.longitude as longitudeGroupeAdresse
FROM _evenementEvenement ee
LEFT JOIN _adresseEvenement ae on ae.idEvenement








SELECT distinct he1.idEvenement as idEvenement, ae.idAdresse as idAdresse, ha.latitude as latitudeGroupeAdresse, ha.longitude as longitudeGroupeAdresse
FROM _adresseEvenement ae
LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
LEFT JOIN historiqueAdresse ha on ha.idAdresse = ae.idAdresse
GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
ORDER BY he1.dateDebut,he1.idHistoriqueEvenement


*/