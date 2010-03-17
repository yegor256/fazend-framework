--
--
--

CREATE TABLE IF NOT EXISTS `fz_source` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of the row",
    `name` VARBINARY(255) NOT NULL COMMENT "Name of the source, unique for the metric",
    `fz_metric` INT UNSIGNED NOT NULL COMMENT "ID of the metric for this source",
    PRIMARY KEY (`id`),
    KEY (`fz_metric`, `name`),
    CONSTRAINT `fz_source_metric` FOREIGN KEY (`fz_metric`) REFERENCES `fz_metric` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;

