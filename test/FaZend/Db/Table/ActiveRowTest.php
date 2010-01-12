<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'AbstractTestCase.php';

class FaZend_Db_Table_ActiveRowTest extends AbstractTestCase {
    
    public function testCreationWorks ()
    {
        $owner = new Model_Owner(132);

        $this->assertEquals(true, $owner->exists());

        $product = new Model_Product();
        $product->text = 'just test';
        $product->owner = $owner;
        $product->save();
    }

    public function testGettingWorks ()
    {
        $product = new Model_Product(10);
        
        $this->assertNotEquals(false, $product->owner, "Owner is null, why?");

        $name = $product->owner->name;
        
        $this->assertNotEquals(false, $name, "Owner name is false, why?");
    }

    public function testRetrieveWorks ()
    {
        $list = Model_Owner::retrieve()
            ->where('name is not null')
            ->setRowClass('Model_Owner')
            ->fetchRow();

        $list->isMe();
    }

    public function testDynamicExceptionWorks ()
    {
        try {
            $list = Model_Owner::retrieve()
                ->where('id = 132')
                ->setRowClass('Model_Owner')
                ->fetchRow();

            // everything ok!

        } catch (Model_Owner_NotFoundException $e) {
            
            $this->fail('no exception, why?');

        }    

        try {
            $list = Model_Owner::retrieve()
                ->where('id = 888')
                ->setRowClass('Model_Owner')
                ->fetchRow();

            $this->fail('no exception, why?');
                
        } catch (Model_Owner_NotFoundException $e) {
            
            // everything ok!

        }    
    }

    public function testTableWithoutIDWorks ()
    {
        $list = FaZend_Db_Table_ActiveRow_car::retrieve()
            ->fetchAll();

        $list = FaZend_Db_Table_ActiveRow_car::retrieve()
            ->fetchPairs();
    }

    public function testTableWithoutPrimaryKeyWorks ()
    {
        $list = FaZend_Db_Table_ActiveRow_boat::retrieve()
            ->fetchAll();

        $boat = new FaZend_Db_Table_ActiveRow_boat (1);
    }

    public function testTableWithoutAnyKeyDoesntWork ()
    {
        try {
            $list = FaZend_Db_Table_ActiveRow_flower::retrieve()
                ->fetchAll();

            $this->fail('no exception, why?');    
        } catch (FaZend_Db_Wrapper_NoIDFieldException $e) {

            // it's OK.
        }    
    }

    public function testDeleteRowWorks()
    {
        $owner = new Model_Owner(132);
        $owner->delete();
    }

    public function testDeleteRowsetWorks()
    {
         FaZend_Db_Table_ActiveRow_car::retrieve()
            ->where('1 = 1')
            ->where('2 = 2')
            ->delete();
    }

}
