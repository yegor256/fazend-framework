--
-- All objects are saved in fzObject, without properties, versions
-- or any other attributes. Here we just keep the ID and the CLASS.
--
-- Every object may have a number of SNAPSHOTS, stored as rows in 
-- fzSnapshot table.
--
--

CREATE TABLE IF NOT EXISTS `fzObject` (

    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of this object",
    `class` VARBINARY(255) NOT NULL COMMENT "Class name in PHP5",

    PRIMARY KEY (`id`)

    ) 
    AUTO_INCREMENT=1 
    DEFAULT CHARSET=utf8 
    ENGINE=InnoDB
    COMMENT="Collection of POS objects";


