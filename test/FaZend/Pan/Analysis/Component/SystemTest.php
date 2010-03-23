<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Analysis_Component_SystemTest extends AbstractTestCase
{
    
    public static function providerComponentsSystemWide()
    {
        return array(
            array('bootstrap.php', 'System.FaZend.bootstrap-php'),
            array('database/owner.sql', 'System.1-owner-sql'),
            array(
                'IndexController::tableAction()',
                'System.FaZend.application.controllers.IndexController.tableAction'
            ),
            array('Model_User', 'System.FaZend.application.Model.Model_User'),
            array('strange link', null),
        );
    }
    
    /**
     * @dataProvider providerComponentsSystemWide
     */
    public function testFindByTraceSystemWideWorks($tag, $component)
    {
        $sys = FaZend_Pan_Analysis_Component_System::getInstance();
        $found = $sys->findByTrace($tag, $sys);
        $this->assertEquals($component, $found);
    }

    public static function providerComponentsClassWide()
    {
        return array(
            array('$this->create()', 'System.FaZend.application.Model.Model_Owner.create'),
            array('self::create()', 'System.FaZend.application.Model.Model_Owner.create'),
            array('strange link', null),
        );
    }
    
    /**
     * @dataProvider providerComponentsClassWide
     */
    public function testFindByTraceClassWideWorks($tag, $component)
    {
        $sys = FaZend_Pan_Analysis_Component_System::getInstance();
        $class = $sys->findByFullName('System.FaZend.application.Model.Model_Owner');
        $found = $sys->findByTrace($tag, $class);
        $this->assertEquals($component, $found);
    }

}
