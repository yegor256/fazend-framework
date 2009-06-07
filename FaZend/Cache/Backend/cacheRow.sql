CREATE TABLE IF NOT EXISTS `__cacherow` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of the data row",
	`label` VARBINARY(1024) NOT NULL COMMENT "Label of the data",
	`data` LARGETEXT NOT NULL COMMENT "Value of data",
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "When this row was created",
	`lifetime` INT NOT NULL COMMENT "Time in seconds to live (FALSE = live forever)",
	PRIMARY KEY USING BTREE (`id`),
	INDEX (`label`)
	) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=Memory;
