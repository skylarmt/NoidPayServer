SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `c0noidpay` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `c0noidpay` ;

-- -----------------------------------------------------
-- Table `c0noidpay`.`balancetypes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`balancetypes` (
  `typeid` INT(11) NOT NULL AUTO_INCREMENT,
  `typename` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`typeid`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`merchants`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`merchants` (
  `merchantid` INT(11) NOT NULL AUTO_INCREMENT,
  `merchantname` VARCHAR(255) NOT NULL,
  `merchantaddress` VARCHAR(1000) NULL DEFAULT NULL,
  `merchantcontact` VARCHAR(1000) NULL DEFAULT NULL,
  `stripesk` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`merchantid`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`users` (
  `userid` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`userid`, `username`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`balances`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`balances` (
  `users_userid` INT(11) NOT NULL,
  `balancetypes_typeid` INT(11) NOT NULL,
  `balance` DECIMAL(10,4) NOT NULL,
  `merchants_merchantid` INT(11) NOT NULL,
  PRIMARY KEY (`users_userid`, `balancetypes_typeid`),
  INDEX `fk_Balances_Balance Types1_idx` (`balancetypes_typeid` ASC),
  INDEX `fk_balances_merchants1_idx` (`merchants_merchantid` ASC),
  CONSTRAINT `fk_Balances_Balance Types1`
    FOREIGN KEY (`balancetypes_typeid`)
    REFERENCES `c0noidpay`.`balancetypes` (`typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_balances_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Balances_Users1`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`deals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`deals` (
  `dealid` INT(11) NOT NULL AUTO_INCREMENT,
  `merchantid` INT(11) NOT NULL,
  `dealtitle` VARCHAR(100) NOT NULL,
  `dealhasurl` TINYINT(1) NOT NULL DEFAULT '0',
  `dealurl` VARCHAR(1000) NOT NULL DEFAULT 'about:blank',
  `dealhtml` VARCHAR(9999) NOT NULL,
  `validafter` DATETIME NOT NULL,
  `validbefore` DATETIME NOT NULL,
  PRIMARY KEY (`dealid`, `merchantid`),
  INDEX `fk_deals_merchants1` (`merchantid` ASC),
  CONSTRAINT `fk_deals_merchants1`
    FOREIGN KEY (`merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 25
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`membership`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`membership` (
  `users_userid` INT(11) NOT NULL,
  `merchants_merchantid` INT(11) NOT NULL,
  PRIMARY KEY (`users_userid`, `merchants_merchantid`),
  INDEX `fk_Users_has_Companies_Users_idx` (`users_userid` ASC),
  INDEX `fk_membership_merchants1_idx` (`merchants_merchantid` ASC),
  CONSTRAINT `fk_membership_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Users_has_Companies_Users`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`merchantlogins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`merchantlogins` (
  `merchants_merchantid` INT(11) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`merchants_merchantid`, `username`),
  CONSTRAINT `fk_merchantlogins_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`transactionstatus`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`transactionstatus` (
  `statuscode` INT(11) NOT NULL,
  `statusname` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`statuscode`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`transactions` (
  `transid` INT(11) NOT NULL AUTO_INCREMENT,
  `userid` INT(11) NULL DEFAULT NULL,
  `merchantid` INT(11) NULL DEFAULT NULL,
  `transamt` DECIMAL(10,4) NULL DEFAULT NULL,
  `balancetypes_typeid` INT(11) NULL DEFAULT NULL,
  `statuscode` INT(11) NOT NULL DEFAULT '1',
  `transdate` DATE NULL DEFAULT NULL,
  `transcompletedate` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`transid`),
  INDEX `fk_Transactions_Users1_idx` (`userid` ASC),
  INDEX `fk_Transactions_Merchants1_idx` (`merchantid` ASC),
  INDEX `fk_Transactions_Balance Types1_idx` (`balancetypes_typeid` ASC),
  INDEX `fk_transactions_transactionstatus1_idx` (`statuscode` ASC),
  CONSTRAINT `fk_Transactions_Balance Types1`
    FOREIGN KEY (`balancetypes_typeid`)
    REFERENCES `c0noidpay`.`balancetypes` (`typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Transactions_Merchants1`
    FOREIGN KEY (`merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transactions_transactionstatus1`
    FOREIGN KEY (`statuscode`)
    REFERENCES `c0noidpay`.`transactionstatus` (`statuscode`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Transactions_Users1`
    FOREIGN KEY (`userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 42
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `c0noidpay`.`userprefs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `c0noidpay`.`userprefs` (
  `users_userid` INT(11) NOT NULL,
  `sendemailontransaction` TINYINT(1) NOT NULL DEFAULT '1',
  `pincode` VARCHAR(8) NULL DEFAULT '0000',
  `pincodeenabled` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`users_userid`),
  CONSTRAINT `fk_UserPrefs_Users1`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
