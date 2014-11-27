-- File : interet.sql
-- Add interests links between tables (auto generated sql from MCD)
-- Author : Antoine Rota Graziosi

CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetAdresse` (
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `idHistoriqueAdresse` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk__interetAdresse_utilisateur1_idx` (`idUtilisateur` ASC),
  INDEX `fk__interetAdresse_historiqueAdresse1_idx` (`idHistoriqueAdresse` ASC),
  CONSTRAINT `fk__interetAdresse_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetAdresse_historiqueAdresse1`
    FOREIGN KEY (`idHistoriqueAdresse`)
    REFERENCES `archi_v2`.`historiqueAdresse` (`idHistoriqueAdresse`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetRue`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetRue` (
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `idRue` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk__interetRue_utilisateur1_idx` (`idUtilisateur` ASC),
  INDEX `fk__interetRue_rue1_idx` (`idRue` ASC),
  PRIMARY KEY (`idUtilisateur`, `idRue`),
  CONSTRAINT `fk__interetRue_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetRue_rue1`
    FOREIGN KEY (`idRue`)
    REFERENCES `archi_v2`.`rue` (`idRue`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetSousQuartier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetSousQuartier` (
  `idSousQuartier` INT(10) UNSIGNED NOT NULL,
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk_interetSousQuartier_sousQuartier1_idx` (`idSousQuartier` ASC),
  INDEX `fk_interetSousQuartier_utilisateur1_idx` (`idUtilisateur` ASC),
  PRIMARY KEY (`idSousQuartier`, `idUtilisateur`),
  CONSTRAINT `fk_interetSousQuartier_sousQuartier1`
    FOREIGN KEY (`idSousQuartier`)
    REFERENCES `archi_v2`.`sousQuartier` (`idSousQuartier`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_interetSousQuartier_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetQuartier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetQuartier` (
  `idQuartier` INT(10) UNSIGNED NOT NULL,
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk__interetQuartier_quartier1_idx` (`idQuartier` ASC),
  INDEX `fk__interetQuartier_utilisateur1_idx` (`idUtilisateur` ASC),
  PRIMARY KEY (`idQuartier`, `idUtilisateur`),
  CONSTRAINT `fk__interetQuartier_quartier1`
    FOREIGN KEY (`idQuartier`)
    REFERENCES `archi_v2`.`quartier` (`idQuartier`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetQuartier_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetPays`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetPays` (
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `idPays` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk__interetPays_utilisateur1_idx` (`idUtilisateur` ASC),
  INDEX `fk__interetPays_pays1_idx` (`idPays` ASC),
  PRIMARY KEY (`idUtilisateur`, `idPays`),
  CONSTRAINT `fk__interetPays_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetPays_pays1`
    FOREIGN KEY (`idPays`)
    REFERENCES `archi_v2`.`pays` (`idPays`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetVille`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetVille` (
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `idVille` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk__interetVille_utilisateur1_idx` (`idUtilisateur` ASC),
  INDEX `fk__interetVille_ville1_idx` (`idVille` ASC),
  PRIMARY KEY (`idUtilisateur`, `idVille`),
  CONSTRAINT `fk__interetVille_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetVille_ville1`
    FOREIGN KEY (`idVille`)
    REFERENCES `archi_v2`.`ville` (`idVille`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetPersonne`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetPersonne` (
  `idPersonne` INT(10) UNSIGNED NOT NULL,
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `created` DATETIME NULL,
  INDEX `fk_table1_personne1_idx` (`idPersonne` ASC),
  INDEX `fk_table1_utilisateur1_idx` (`idUtilisateur` ASC),
  PRIMARY KEY (`idPersonne`, `idUtilisateur`),
  CONSTRAINT `fk_table1_personne1`
    FOREIGN KEY (`idPersonne`)
    REFERENCES `archi_v2`.`personne` (`idPersonne`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_table1_utilisateur1`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;