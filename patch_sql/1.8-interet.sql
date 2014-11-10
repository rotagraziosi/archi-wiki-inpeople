-- File : interet.sql
-- Add interests links between tables (auto generated sql from MCD)
-- Author : Antoine Rota Graziosi


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetAdresse`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetAdresse` (
  `idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `idHistoriqueAdresse` INT(10) UNSIGNED NOT NULL,
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
  `utilisateur_idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `rue_idRue` INT(10) UNSIGNED NOT NULL,
  INDEX `fk__interetRue_utilisateur1_idx` (`utilisateur_idUtilisateur` ASC),
  INDEX `fk__interetRue_rue1_idx` (`rue_idRue` ASC),
  CONSTRAINT `fk__interetRue_utilisateur1`
    FOREIGN KEY (`utilisateur_idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetRue_rue1`
    FOREIGN KEY (`rue_idRue`)
    REFERENCES `archi_v2`.`rue` (`idRue`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetSousQuartier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetSousQuartier` (
  `sousQuartier_idSousQuartier` INT(10) UNSIGNED NOT NULL,
  `utilisateur_idUtilisateur` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_interetSousQuartier_sousQuartier1_idx` (`sousQuartier_idSousQuartier` ASC),
  INDEX `fk_interetSousQuartier_utilisateur1_idx` (`utilisateur_idUtilisateur` ASC),
  CONSTRAINT `fk_interetSousQuartier_sousQuartier1`
    FOREIGN KEY (`sousQuartier_idSousQuartier`)
    REFERENCES `archi_v2`.`sousQuartier` (`idSousQuartier`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_interetSousQuartier_utilisateur1`
    FOREIGN KEY (`utilisateur_idUtilisateur`)
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
  INDEX `fk__interetQuartier_quartier1_idx` (`idQuartier` ASC),
  INDEX `fk__interetQuartier_utilisateur1_idx` (`idUtilisateur` ASC),
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
  `utilisateur_idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `pays_idPays` INT(10) UNSIGNED NOT NULL,
  INDEX `fk__interetPays_utilisateur1_idx` (`utilisateur_idUtilisateur` ASC),
  INDEX `fk__interetPays_pays1_idx` (`pays_idPays` ASC),
  CONSTRAINT `fk__interetPays_utilisateur1`
    FOREIGN KEY (`utilisateur_idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetPays_pays1`
    FOREIGN KEY (`pays_idPays`)
    REFERENCES `archi_v2`.`pays` (`idPays`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `archi_v2`.`_interetVille`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `archi_v2`.`_interetVille` (
  `utilisateur_idUtilisateur` INT(10) UNSIGNED NOT NULL,
  `ville_idVille` INT(10) UNSIGNED NOT NULL,
  INDEX `fk__interetVille_utilisateur1_idx` (`utilisateur_idUtilisateur` ASC),
  INDEX `fk__interetVille_ville1_idx` (`ville_idVille` ASC),
  CONSTRAINT `fk__interetVille_utilisateur1`
    FOREIGN KEY (`utilisateur_idUtilisateur`)
    REFERENCES `archi_v2`.`utilisateur` (`idUtilisateur`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk__interetVille_ville1`
    FOREIGN KEY (`ville_idVille`)
    REFERENCES `archi_v2`.`ville` (`idVille`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
