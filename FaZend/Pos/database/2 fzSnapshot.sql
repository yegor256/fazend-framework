--
-- Every object from fzObject may have an unlimited number of
-- snapshots. Every snapshot is a version of the object edited by some
-- particular user (or by anonymous user). Every snapshot has a version,
-- list of PHP-class properties and values (in associative array),
-- and date/time information.
--
-- When you want to change the object, you just create a new snapshot,
-- link it with the object and gives it a version which is bigger than
-- all previous versions.
--

CREATE TABLE IF NOT EXISTS `fzSnapshot` (
    
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of this snapshot",

    -- this snapshot is attached to an object
    -- and saves the latest copy of the data from that object
    `fzObject` INT UNSIGNED NOT NULL COMMENT "Unique ID of the object for this snapshot, FK to fzObject",
    
    -- every snapshot has information about the data
    -- and the data themselves
    `properties` LONGTEXT BINARY COMMENT "Serialized copy of the PHP object properties (array), NULL = no changes",
    `version` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT "Version number of the snapshot",
    `alive` BOOLEAN NOT NULL DEFAULT 1 COMMENT "The object is still alive = TRUE",
    `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "When this snapshot was made",
    `user` INT UNSIGNED COMMENT "Author of the update, if any",
    `comment` TEXT COMMENT "Optional comment for the change made",
    `baselined` BOOLEAN NOT NULL DEFAULT 0 COMMENT "The object is baselined (the app may block changes)",

    PRIMARY KEY(`id`),
    FOREIGN KEY(`fzObject`) REFERENCES `fzObject`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(`user`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    -- unique version number for any particular object
    UNIQUE (`fzObject`, `version`)

    ) 
    AUTO_INCREMENT=1 
    DEFAULT CHARSET=utf8 
    ENGINE=InnoDB
    COMMENT="Collection of POS object snapshots";

