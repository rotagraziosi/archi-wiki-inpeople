-- Full text research
-- This need the update-histo-adresse.sql script to be run before
-- Author: Antoine Rota Graziosi

CREATE TABLE recherchetest AS 
  (
        SELECT distinct ee.idEvenement as idEvenementGA ,
                r.nom as nomRue,
                sq.nom as nomSousQuartier,
                q.nom as nomQuartier,
                v.nom as nomVille,
                p.nom as nomPays,
                ha1.idRue, 
                r.prefixe as prefixeRue,
                r.idSousQuartier AS idSousQuartier,
                ha1.idQuartier AS idQuartier,
                ha1.idVille  AS idVille,
                ha1.idPays AS idPays,
		he1.description as description,
		he1.titre as titre,
		pers.nom as nomPersonne,
		pers.prenom as prenomPersonne,
                CONVERT( ha1.numero USING utf8 ) as numeroAdresse,
                ha1.idHistoriqueAdresse,
                ha1.idIndicatif as idIndicatif,
		ind.nom as nomIndicatif,
		he1.idSource as idSource,
		he1.idTypeStructure as idTypeStructure,
		he1.dateDebut as dateDebut,
		he1.dateFin as dateFin,
		he1.MH as MH,
		he1.ISMH as ISMH

        
        FROM historiqueAdresse ha2, historiqueAdresse ha1
        LEFT JOIN rue r         ON r.idRue = ha1.idRue
        LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = ha1.idSousQuartier 
        LEFT JOIN quartier q        ON q.idQuartier = ha1.idQuartier 
        LEFT JOIN ville v        ON v.idVille = ha1.idVille 
        LEFT JOIN pays p        ON p.idPays = ha1.idPays 
        
        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
        LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
        
        LEFT JOIN _evenementPersonne ep ON ep.idEvenement = he1.idEvenement
        LEFT JOIN personne pers ON pers.idPersonne = ep.idPersonne
        LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
        
        WHERE ha2.idAdresse = ha1.idAdresse 

        AND ae.idAdresse IS NOT NULL
        GROUP BY ha1.idAdresse, he1.idEvenement, ha1.idHistoriqueAdresse,  he1.idHistoriqueEvenement
        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
);


-- Change db engine

ALTER TABLE recherchetmp ENGINE=MYISAM;


-- Adding the index for the fulltext search
ALTER TABLE recherche ADD FULLTEXT INDEX `search` (nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, prefixeRue, description, titre , nomPersonne, prenomPersonne);
ALTER TABLE recherchetmp ADD FULLTEXT INDEX `search` (nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, numeroAdresse, prefixeRue, description, titre , nomPersonne, prenomPersonne, concat1,concat2,concat3);



                
                    nomQuartier LIKE "%vélo%"
                    OR nomSousQuartier LIKE "%vélo%"
                    OR nomVille LIKE "%vélo%"
                    OR nomPays LIKE "%vélo%"
                    OR nomRue LIKE "%vélo%"
                    OR titre LIKE "%vélo%"
                    OR description LIKE "%vélo%" 


ALTER TABLE recherchetmp ADD FULLTEXT INDEX `search2` (nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, description, titre );




-- Executing the fulltext search 

SELECT idEvenementGA, nomRue,nomSousQuartier,nomQuartier,nomVille,nomPays,prefixeRue,description,titre,nomPersonne, prenomPersonne, numeroAdresse,concat1,concat2,concat3 ,
(
	10 * (MATCH (nomRue) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	10 * (MATCH (nomSousQuartier) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	10 * (MATCH (nomQuartier) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	10 * (MATCH (nomVille) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	10 * (MATCH (nomPays) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	1 * (MATCH (description) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	1 * (MATCH (titre) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	1000 * (MATCH (concat1) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	100 * (MATCH (concat2) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) +
	100 * (MATCH (concat3) AGAINST ('5 rue des balayeurs' IN BOOLEAN MODE)) 

) as relevance

FROM recherchetmp
WHERE MATCH(nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, prefixeRue,numeroAdresse,  description, titre , nomPersonne, prenomPersonne, concat1,concat2,concat3) AGAINST ('5 rue des balayeurs')
ORDER BY relevance DESC
;





SELECT idEvenementGA, nomRue,nomSousQuartier,nomQuartier,nomVille,nomPays,prefixeRue,description,titre,nomPersonne, prenomPersonne, numeroAdresse,concat1,concat2,concat3
FROM recherchetmp
WHERE MATCH(nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, description, titre ) AGAINST ('etoile');


