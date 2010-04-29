<?php

class Injector extends FaZend_Test_Injector
{

    protected function _injectPos() 
    {
        $this->_bootstrap('db');
        $this->_bootstrap('fz_deployer');
        $this->_bootstrap('fz_orm');

        // // We should work with our own mock root object
        FaZend_Pos_Properties::setRootClass('Model_Pos_Root');
        FaZend_Pos_Properties::root();
    }

}