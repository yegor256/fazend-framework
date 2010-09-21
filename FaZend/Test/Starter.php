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

/**
 * Test starter parent class, abstract
 *
 * You should inherit this class and place your starter into
 * /test/starter/Starter.php. Your class should be called "Starter".
 * Method Starter::start() will be executed in building process, before
 * all unit tests.
 *
 * In your class you should define _start*() methods. All of them will
 * be called from ::run().
 *
 * This class is a good place for:
 *  - cleaning the database (dropping of all tables)
 *  - cleaning all system files and directories
 *
 * @see build.xml
 * @see FaZend_Test_Injector
 * @see FaZend_Application_Resource_Fazend::_initTestInjection()
 * @package Test
 */
abstract class FaZend_Test_Starter
{

    /**
     * Resource who is running us now
     *
     * @var FaZend_Application_Resource_fz_starter
     */
    private $_resource;
    
    /**
     * Set resource
     *
     * @param FaZend_Application_Resource_fz_starter
     * @return void
     */
    public final function setResource(FaZend_Application_Resource_fz_starter $resource) 
    {
        $this->_resource = $resource;
    }

    /**
     * Make all initializations before tests
     *
     * @return void
     */
    public final function start() 
    {
        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_start/', $method->getName())) {
                $this->{$method->getName()}();
            }
        }
    }
    
    /**
     * Bootstrap given resource
     *
     * You may need this method when some resource should be loaded
     * BEFORE injector. Injector is executed before all other resources
     * loading, that's why you may need to bootstrap something explicitly.
     *
     * @param string Name of the resource to bootstrap
     * @return mixed
     */
    protected function _bootstrap($resource) 
    {
        return $this->_resource->getBootstrap()->bootstrap($resource);
    }

    /**
     * Drop entire database, including all TABLE-s and VIEW-s
     *
     * @return void
     * @throws FaZend_Test_Starter_Exception
     */
    protected function _dropDatabase() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $dropped = array();
        $present = $db->listTables();
        while (count($present)) {
            $before = count($present);
            foreach ($present as $id=>$table) {
                foreach (array('DROP TABLE %s', 'DROP VIEW %s') as $q) {
                    try {
                        $db->query(sprintf($q, $db->quoteIdentifier($table)));
                        $dropped[] = $table;
                        unset($present[$id]);
                    } catch (Exception $e) {
                        assert($e instanceof Exception);
                        // just swallow it
                    }
                }
            }
            // maybe we failed to delete everything?
            if (count($present) == $before) {
                FaZend_Exception::raise(
                    'FaZend_Test_Starter_Exception',
                    $e->getMessage()
                );
            }
        }
        if ($dropped) {
            logg(
                'TABLEs/VIEWs dropped: %s', 
                implode(', ', $dropped)
            );
        } else {
            logg('No TABLEs/VIEWs to drop');
        }
    }

}
