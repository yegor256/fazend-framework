--
-- Rows in fzPartOf are at the same time instances of
-- class fzObject. That's why fzObject column is a primary key
-- and at the same time is a FK to fzObject table. When you want to
-- create a row in fzPartOf you should first create a row in fzObject.
-- And then you should link fzPartOf and fzObject.
--
-- That will give the ability to work with links between objects
-- the same way as you work with objects (trace versions, approve, baseline, etc.)
--

CREATE TABLE IF NOT EXISTS `fzPartOf` (
    
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of this row",

    `name` VARBINARY(255) NOT NULL COMMENT "Unique name of the relation",

--    `fzObject` INT UNSIGNED NOT NULL COMMENT "Unique ID of this object, FK to fzObject",
    `parent` INT UNSIGNED NOT NULL COMMENT "ID of the parent object, FK to fzObject",
    `kid` INT UNSIGNED NOT NULL COMMENT "ID of the child object, FK to fzObject",

    PRIMARY KEY(`id`),
--    PRIMARY KEY(`fzObject`),
--    FOREIGN KEY(`fzObject`) REFERENCES `fzObject`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(`parent`) REFERENCES `fzObject`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(`child`) REFERENCES `fzObject`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,

    -- relation name is unique for any particular object
    UNIQUE(`parent`, `name`)

    ) 
    AUTO_INCREMENT=1 
    DEFAULT CHARSET=utf8 
    ENGINE=InnoDB
    COMMENT="Part-of relations between POS objects";

