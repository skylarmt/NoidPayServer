SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `c0noidpay` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `c0noidpay` ;

-- -----------------------------------------------------
-- Table `c0noidpay`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`users` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`users` (
  `userid` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`userid`, `username`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`merchants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`merchants` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`merchants` (
  `merchantid` INT NOT NULL AUTO_INCREMENT,
  `merchantname` VARCHAR(255) NOT NULL,
  `merchantaddress` VARCHAR(1000) NULL,
  `merchantcontact` VARCHAR(1000) NULL,
  `stripesk` VARCHAR(200) NULL,
  PRIMARY KEY (`merchantid`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`balancetypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`balancetypes` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`balancetypes` (
  `typeid` INT NOT NULL AUTO_INCREMENT,
  `typename` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`typeid`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`transactionstatus`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`transactionstatus` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`transactionstatus` (
  `statuscode` INT NOT NULL,
  `statusname` VARCHAR(100) NULL,
  PRIMARY KEY (`statuscode`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`transactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`transactions` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`transactions` (
  `transid` INT NOT NULL AUTO_INCREMENT,
  `userid` INT NULL,
  `merchantid` INT NULL,
  `transamt` DECIMAL(10,4) NULL,
  `balancetypes_typeid` INT NULL,
  `statuscode` INT NOT NULL DEFAULT 1,
  `transdate` DATE NULL,
  `transcompletedate` DATE NULL,
  PRIMARY KEY (`transid`),
  INDEX `fk_Transactions_Users1_idx` (`userid` ASC),
  INDEX `fk_Transactions_Merchants1_idx` (`merchantid` ASC),
  INDEX `fk_Transactions_Balance Types1_idx` (`balancetypes_typeid` ASC),
  INDEX `fk_transactions_transactionstatus1_idx` (`statuscode` ASC),
  CONSTRAINT `fk_Transactions_Users1`
    FOREIGN KEY (`userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Transactions_Merchants1`
    FOREIGN KEY (`merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Transactions_Balance Types1`
    FOREIGN KEY (`balancetypes_typeid`)
    REFERENCES `c0noidpay`.`balancetypes` (`typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transactions_transactionstatus1`
    FOREIGN KEY (`statuscode`)
    REFERENCES `c0noidpay`.`transactionstatus` (`statuscode`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`balances`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`balances` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`balances` (
  `users_userid` INT NOT NULL,
  `balancetypes_typeid` INT NOT NULL,
  `balance` DECIMAL(10,4) NOT NULL,
  `merchants_merchantid` INT NOT NULL,
  PRIMARY KEY (`users_userid`, `balancetypes_typeid`),
  INDEX `fk_Balances_Balance Types1_idx` (`balancetypes_typeid` ASC),
  INDEX `fk_balances_merchants1_idx` (`merchants_merchantid` ASC),
  CONSTRAINT `fk_Balances_Users1`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Balances_Balance Types1`
    FOREIGN KEY (`balancetypes_typeid`)
    REFERENCES `c0noidpay`.`balancetypes` (`typeid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_balances_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`membership`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`membership` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`membership` (
  `users_userid` INT NOT NULL,
  `merchants_merchantid` INT NOT NULL,
  PRIMARY KEY (`users_userid`, `merchants_merchantid`),
  INDEX `fk_Users_has_Companies_Users_idx` (`users_userid` ASC),
  INDEX `fk_membership_merchants1_idx` (`merchants_merchantid` ASC),
  CONSTRAINT `fk_Users_has_Companies_Users`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_membership_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`userprefs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`userprefs` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`userprefs` (
  `users_userid` INT NOT NULL,
  `sendemailontransaction` TINYINT(1) NOT NULL DEFAULT 1,
  `pincode` VARCHAR(8) NULL DEFAULT '0000',
  `pincodeenabled` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`users_userid`),
  CONSTRAINT `fk_UserPrefs_Users1`
    FOREIGN KEY (`users_userid`)
    REFERENCES `c0noidpay`.`users` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `c0noidpay`.`merchantlogins`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `c0noidpay`.`merchantlogins` ;

CREATE TABLE IF NOT EXISTS `c0noidpay`.`merchantlogins` (
  `merchants_merchantid` INT NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `realname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL,
  PRIMARY KEY (`merchants_merchantid`, `username`),
  CONSTRAINT `fk_merchantlogins_merchants1`
    FOREIGN KEY (`merchants_merchantid`)
    REFERENCES `c0noidpay`.`merchants` (`merchantid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `c0noidpay`.`merchants`
-- -----------------------------------------------------
START TRANSACTION;
USE `c0noidpay`;
INSERT INTO `c0noidpay`.`merchants` (`merchantid`, `merchantname`, `merchantaddress`, `merchantcontact`, `stripesk`) VALUES (1, 'Test Merchant', '123 Address Street', 'Somebody', NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `c0noidpay`.`balancetypes`
-- -----------------------------------------------------
START TRANSACTION;
USE `c0noidpay`;
INSERT INTO `c0noidpay`.`balancetypes` (`typeid`, `typename`) VALUES (1, 'Cash');
INSERT INTO `c0noidpay`.`balancetypes` (`typeid`, `typename`) VALUES (2, 'Tokens');

COMMIT;


-- -----------------------------------------------------
-- Data for table `c0noidpay`.`transactionstatus`
-- -----------------------------------------------------
START TRANSACTION;
USE `c0noidpay`;
INSERT INTO `c0noidpay`.`transactionstatus` (`statuscode`, `statusname`) VALUES (0, 'Complete');
INSERT INTO `c0noidpay`.`transactionstatus` (`statuscode`, `statusname`) VALUES (1, 'Pending');
INSERT INTO `c0noidpay`.`transactionstatus` (`statuscode`, `statusname`) VALUES (2, 'Canceled');
INSERT INTO `c0noidpay`.`transactionstatus` (`statuscode`, `statusname`) VALUES (3, 'Refunded');

COMMIT;

