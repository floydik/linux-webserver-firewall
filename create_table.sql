CREATE TABLE IF NOT EXISTS `incidents` (
`id` INT( 10 ) UNSIGNED NOT NULL ,
`source` VARCHAR( 255 ) NOT NULL ,
`datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP( ) ON UPDATE CURRENT_TIMESTAMP( ) ,
`comment` TEXT NOT NULL ,
`ipv6_id` INT( 10 ) UNSIGNED NOT NULL ,
`ipv4_id` INT( 10 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `semaphore` (
`id` TINYINT( 3 ) UNSIGNED NOT NULL ,
`color` VARCHAR( 24 ) NOT NULL ,
`description` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COMMENT = 'aka traffic light';

CREATE TABLE IF NOT EXISTS `ipv4` (
  `id` INT(10) UNSIGNED NOT NULL,
  `ip` VARCHAR(32) NOT NULL,
  `mask` TINYINT(3) UNSIGNED NOT NULL,
  `updatetime` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `semaphore_id` TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `semaphore_id`),
  INDEX `fk_ipv4_semaphore1_idx` (`semaphore_id` ASC),
  CONSTRAINT `fk_ipv4_semaphore1`
    FOREIGN KEY (`semaphore_id`)
    REFERENCES `semaphore` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `ipv6` (
  `id` INT(10) UNSIGNED NOT NULL,
  `ip` VARCHAR(255) NOT NULL,
  `mask` INT(10) UNSIGNED NOT NULL,
  `updatetime` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `semaphore_id` TINYINT(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `semaphore_id`),
  INDEX `fk_ipv6_semaphore_idx` (`semaphore_id` ASC),
  CONSTRAINT `fk_ipv6_semaphore`
    FOREIGN KEY (`semaphore_id`)
    REFERENCES `semaphore` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `semaphore` (
`id` ,
`color` ,
`description`
)
VALUES ('0', 'white', 'whitelisted IP'), 
('1', 'violet', 'manually delisted'),
('2', 'green', 'known IP, now OK'),
('3', 'yellow', 'temporary blacklisted'),
('4', 'red', 'temporary blacklisted'),
('5', 'black', 'permanently blacklisted');

CREATE TABLE IF NOT EXISTS `rules` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `regex` varchar(512) NOT NULL,
 `log` varchar(120) NOT NULL,
 `threshold` int(11) NOT NULL,
 `execute` int(11) NOT NULL,
 `active` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)) 
ENGINE = InnoDB 
DEFAULT CHARACTER SET = utf8;
