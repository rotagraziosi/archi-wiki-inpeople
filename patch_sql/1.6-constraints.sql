-- 1.6-constraints.sql
-- Add missing constraints
-- Author : Antoine Rota Graziosi



-- Create new table
CREATE TABLE _evenementSource AS 
(
	SELECT s.idSource, he.idEvenement
	FROM source s
	LEFT JOIN historiqueEvenement he on he.idSource = s.idSource
);




-- TODO  : Need to had constraints on historiqueEvenement and source table to _evenementSource table



-- Add missing constraints
ALTER TABLE source ADD CONSTRAINT source_ibfk_1 FOREIGN KEY(idTypeSource) REFERENCES typeSource(idTypeSource);

ALTER TABLE historiqueAdresse ADD CONSTRAINT historiqueAdresse_ibfk_5 FOREIGN KEY(idIndicatif) REFERENCES indicatif(idIndicatif);



-- TODO : Delete idSource from histriqueEvenement

