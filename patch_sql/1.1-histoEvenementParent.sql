-- Added parent field to create two level events
-- Father has field "parent" set to 0 and children set to idEvenement of his parent
-- Orphan events (unlinked to any other events) have "parent" field set to -1

ALTER TABLE `historiqueEvenement` ADD `parent` INT( 10 ) NOT NULL DEFAULT '-1' AFTER `idImagePrincipale` ;
