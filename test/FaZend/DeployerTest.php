<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'AbstractTestCase.php';

class FaZend_DeployerTest extends AbstractTestCase {

    public static function providerSqlSamples() {

        return array(
            array("--\n--\t\n\n--\ncreate table   test (id int unsigned, name5_8 varchar(255) not null comment \"this is what works\");\n\n"),
            array(
                   "CREATE TABLE IF NOT EXISTS `ad` (
                   `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \"Unique ID of the ad\",
                   `text` VARCHAR(250) NOT NULL COMMENT \"Text of the Ad\",
                   `url` VARCHAR(1024) NOT NULL COMMENT \"URL to be show to the reader\",
                   `author` INT(10) UNSIGNED NOT NULL COMMENT \"Author of the ad (id)\",
                   `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \"When this ad was created\",
                   `keyword` INT(10) UNSIGNED NOT NULL COMMENT \"Id of the keyword\",
                   PRIMARY KEY USING BTREE (`id`),
                   CONSTRAINT `fk_ad_keyword` FOREIGN KEY (`keyword`) REFERENCES `keyword` (`id`) ON UPDATE CASCADE,
                   CONSTRAINT `fk_ad_user` FOREIGN KEY (`author`) REFERENCES `user` (`id`) ON UPDATE CASCADE
                   ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;"),
        );

    }

    /**
     * @dataProvider providerSqlSamples
     */
    public function testEmailsAreRenderedAndSent ($sql) {

        $deployer = new Model_Deployer(new Zend_Config(array()));

        $info = $deployer->sqlInfo($sql);

        $this->assertTrue(count($info) > 0);

    }

}
        