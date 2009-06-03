<?php

define('APPLICATION_PATH', realpath('../'));
define('FAZEND_PATH', realpath('../../../FaZend'));

set_include_path('d:/dev/zend-trunk' . PATH_SEPARATOR . get_include_path());

include '../../../FaZend/Application/index.php';
