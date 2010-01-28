<?php

class Injector extends FaZend_Test_Injector
{

    protected function _injectPos() 
    {
        // We should work with our own mock root object
        FaZend_Pos_Properties::setRootClass('Model_Pos_Root');
        FaZend_Pos_Properties::root();
    }

}