--
--
--

CREATE TABLE IF NOT EXISTS `fz_dependency` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of this row",
    `fz_metric` INT UNSIGNED NOT NULL COMMENT "ID of the parent metric",
    `kid` INT UNSIGNED NOT NULL COMMENT "ID of the dependent metric",
    PRIMARY KEY (`id`),
    FOREIGN KEY (`fz_metric`) REFERENCES `fz_metric` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`kid`) REFERENCES `fz_metric` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;

