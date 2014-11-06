-- 1.7-triggers.sql 
-- Create triggers to auto-add value to recherche table when new data are inserted in other tables
-- Author : Antoine Rota Graziosi


-- Insert historiqueEvenement trigger
delimiter $$
create trigger recherche_historiqueEvenement_insert_trig after insert on historiqueEvenement
for each row
begin
	declare done int default false;
	declare each_personne int; 
	declare num_rows int default 0; 
	declare cursorPersonne cursor for select idPersonne from _evenementPersonne where idEvenement = new.idEvenement;
	declare continue handler for sqlstate '02000' set done = 1; 
	
	open cursorPersonne; 
	select found_rows() into num_rows; 
	
	-- Start loop for all the personne selected
	repeat
		fetch cursorPersonne into each_personne;
		
		
	insert into recherche 
		(idEvenementGA, 
		nomRue, 
		nomSousQuartier , 
		nomQuartier , 
		nomVille,
		nomPays,
		idRue,
		prefixeRue,
		idSousQuartier,
		idQuartier,
		idVille,
		idPays,
		description,
		titre,
		nomPersonne,
		prenomPersonne,
		numeroAdresse,
		idHistoriqueAdresse,
		idIndicatif,
		idTypeStructure,
		idTypeEvenement,
		idSource,
		dateDebut,
		dateFin,
		ISMH,
		MH,
		idCourantArchitectural,
		concat1,
		concat2,
		concat3
		) 
SELECT distinct he1.idEvenement as idEvenementGA ,
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
		he1.idTypeStructure as idTypeStructure,
		he1.idTypeEvenement as idTypeEvenement,
		he1.idSource as idSource,
		he1.dateDebut as dateDebut,
		he1.dateFin as dateFin,
		he1.ISMH as ISMH,
		he1.MH as MH,
		eca.idCourantArchitectural as idCourantArchitectural,
		CONCAT_WS( '', he1.titre, CONVERT( ha1.numero USING utf8 ) , r.prefixe, r.nom, sq.nom, q.nom, v.nom, p.nom ) as concat1,
		CONCAT_WS('', pers.nom, pers.prenom) as concat2,
		CONCAT_WS('',pers.prenom , pers.nom) as concat3
        
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
        LEFT JOIN _evenementPersonne ep ON ep.idPersonne = each_personne
        LEFT JOIN personne pers ON pers.idPersonne = ep.idPersonne
        LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
		LEFT JOIN _evenementCourantArchitectural eca on eca.idEvenement = ee.idEvenementAssocie
        
        WHERE ha2.idAdresse = ha1.idAdresse 
        AND ae.idAdresse IS NOT NULL
		AND he1.idEvenement = NEW.idEvenement
		GROUP BY ha1.idAdresse, he1.idEvenement, ha1.idHistoriqueAdresse,  he1.idHistoriqueEvenement
        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement);
        
        until done end repeat;
END$$
delimiter ;




-- Update historiqueEvenement trigger
delimiter $$
create trigger recherche_historiqueEvenement_update_trig after UPDATE on historiqueEvenement
for each row
begin
	
	UPDATE recherche 
	SET
	idTypeStructure=new.idTypeStructure,
	description = new.description,
	titre = new.titre,
	idTypeEvenement = new.idTypeEvenement,
	idSource = new.idSource,
	dateDebut  = new.dateDebut,
	dateFin= new.dateFin,
	ISMH = new.ISMH,
	MH = new.MH
	WHERE idEvenementGA = new.idEvenement; 

END$$

delimiter ;


-- Delete historiqueEvenement trigger
delimiter $$
create trigger recherche_historiqueEvenement_delete_trig after DELETE on historiqueEvenement
for each row
begin
	
	delete from recherche 
	where idEvenementGA = old.idEvenement; 
END$$
delimiter ;

