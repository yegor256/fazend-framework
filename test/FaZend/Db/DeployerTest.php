<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_Db_DeployerTest extends AbstractTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_deployer = new FaZend_Db_Deployer();
        $this->_deployer->setFolders(
            array(APPLICATION_PATH . '/deploy/database')
        );
    }

    public static function providerSqlSamples()
    {
        return array(
            array(
                'test',
                "--\n--\t\n\n--\ncreate table   test (id int unsigned, name5_8 varchar(255) not null
                comment \"this is what works\");\n\n"),
            array(
                'ad',
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
            array(
                'tag',
                "CREATE TABLE IF NOT EXISTS `tag` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \"Unique ID of the tag\",
                    `question` INT(10) UNSIGNED NOT NULL COMMENT \"Id of the question\",
                    `keyword` VARCHAR(80) NOT NULL COMMENT \"Text keyword\",
                    PRIMARY KEY USING BTREE (`id`),
                    UNIQUE (`question`, `keyword`),
                    CONSTRAINT `fk_tag_question` FOREIGN KEY (`question`) REFERENCES `question` (`id`) ON UPDATE CASCADE
                    ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;"
            )
        );
    }

    /**
     * @dataProvider providerSqlSamples
     */
    public function testEmailsAreRenderedAndSent ($table, $sql)
    {
        $info = $this->_deployer->getSqlInfo($sql);
        $this->assertTrue(count($info) > 0, 'No information about the table, why?');

        foreach ($info as $column) {
            $this->assertTrue($column['COLUMN_NAME'] != 'PRIMARY');
        }
    }

    public function testGetTablesWorks()
    {
        $list = $this->_deployer->getTables();
        $this->assertGreaterThan(0, count($list));
    }

    public function testGetTableInfoWorks()
    {
        $list = $this->_deployer->getTables();
        $this->assertGreaterThan(0, count($list));
        $table = array_shift($list);
        $info = $this->_deployer->getTableInfo($table);
    }

}
