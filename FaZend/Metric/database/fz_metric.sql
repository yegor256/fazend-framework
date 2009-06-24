--
-- 
-- 

CREATE TABLE IF NOT EXISTS `fz_metric` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of the row",
    `method` VARBINARY(255) NOT NULL COMMENT "'class::method' combination of the calculation function",
    `params` LONGTEXT BINARY COMMENT "Serialized associative array of params to be passed",
    `value` LONGTEXT BINARY COMMENT "Result value of the function, serialized",
    `environment` VARBINARY(32) NOT NULL COMMENT "Unique ID of the environment",
    `msec` INT UNSIGNED COMMENT "Time used to calculate the value",
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT "Date of this metric calculation",
    PRIMARY KEY (`id`),
    KEY (`name`, `environment`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;

