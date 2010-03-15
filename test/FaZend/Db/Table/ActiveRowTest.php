<?php

require_once 'AbstractTestCase.php';

class FaZend_Db_Table_ActiveRowTest extends AbstractTestCase
{
    
    public function testCreationWorks ()
    {
        $owner = new Model_Owner(132);

        $this->assertEquals(true, $owner->exists());

        $product = new Model_Product();
        $product->text = 'just test';
        $product->owner = $owner;
        $product->save();
    }

    public function testGettingWorks()
    {
        $product = new Model_Product(10);
        $this->assertNotEquals(false, $product->owner, "Owner is null, why?");
        $this->assertTrue($product->owner instanceof Model_Owner, "Owner is invalid");

        $name = $product->owner->name;
        $this->assertTrue(is_string($name), "Owner name is not STRING, why?");
    }

    public function testClassMappingWorks()
    {
        $owner = Model_Owner::create('peter');
        $this->assertTrue(
            $owner->created instanceof Zend_Date, 
            "CREATED is of invalid type: " . gettype($owner->created)
        );
    }

    public function testRetrieveWorks()
    {
        $owner = Model_Owner::retrieve()
            ->where('name is not null')
            ->setRowClass('Model_Owner')
            ->fetchRow();

        $this->assertTrue(is_bool($owner->isMe()));
    }

    public function testDynamicBindingWorks()
    {
        Model_Owner::create('john');
        $cnt = count(
            Model_Owner::retrieve()
            ->where('name = :name OR name = :name')
            ->fetchAll(array('name' => 'john'))
        );
        $this->assertEquals(1, $cnt, 'No rows in the DB? Impossible!');
        
        $list = Model_Owner::retrieve()
            ->where('name = :name OR name = :name')
            ->fetchAll(array('name' => 'john'));
        $cnt = 0;
        foreach ($list as $i)
            $cnt++;
        $this->assertEquals(1, $cnt, 'No rows in the DB? Impossible!');

        $cnt = count(
            Model_Owner::retrieve()
            ->where('name = :name OR name = :name')
            ->fetchPairs(array('name' => 'john'))
        );
        $this->assertEquals(1, $cnt, 'No rows in the DB? Impossible!');

        $cnt = count(
            Model_Owner::retrieve()
            ->where('name = :name OR name = :name')
            ->fetchOne(array('name' => 'john'))
        );
        $this->assertEquals(1, $cnt, 'No rows in the DB? Impossible!');

        $owner = Model_Owner::retrieve()
            ->where('name = :name OR name = :name')
            ->fetchRow(array('name' => 'john'));
        $this->assertEquals('john', $owner->name, 'Name of the owner is wrong, hm...');
    }

    /**
     * @expectedException Model_Owner_NotFoundException
     */
    public function testDynamicExceptionWorks()
    {
        $list = Model_Owner::retrieve()
            ->where('id = 888')
            ->setRowClass('Model_Owner')
            ->fetchRow();
    }

    public function testTableWithoutIDWorks()
    {
        $list = FaZend_Db_Table_ActiveRow_car::retrieve()
            ->fetchAll();
        $list = FaZend_Db_Table_ActiveRow_car::retrieve()
            ->fetchPairs();
    }

    public function testTableWithoutPrimaryKeyWorks()
    {
        $list = FaZend_Db_Table_ActiveRow_boat::retrieve()
            ->fetchAll();
        $boat = new FaZend_Db_Table_ActiveRow_boat(1);
    }

    /**
     * @expectedException FaZend_Db_Wrapper_NoIDFieldException
     */
    public function testTableWithoutAnyKeyDoesntWork()
    {
        $list = FaZend_Db_Table_ActiveRow_flower::retrieve()
            ->fetchAll();
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
        $this->assertEquals(0, count(FaZend_Db_Table_ActiveRow_car::retrieve()->fetchAll()));
    }
    
    public function testUpdateRowsetWorks()
    {
         Model_Owner::retrieve()
            ->where('1 = 1')
            ->where('2 = 2')
            ->update(array('name' => 'test'));
            
        $owner = new Model_Owner(132);
        $this->assertEquals('test', $owner->name);
    }
    
    public function testFlyweightProperlyAllocateObjects()
    {
        $owner = new Model_Owner(132);
        $product = new Model_Product(10);
        
        $this->assertTrue(
            $owner === $product->owner, 
            "Objects are different, but they should be the same"
        );
    }

    public function testCleanStatusIsCorrect()
    {
        // $owner = new Model_Owner(132);
        // $this->assertTrue($owner->isClean());
        
        $owner = new Model_Owner();
        $this->assertFalse($owner->isClean());
        
        $owner->name = 'test';
        $this->assertFalse($owner->isClean());
        
        $owner->save();
        $this->assertTrue($owner->isClean());
    }
    
}
