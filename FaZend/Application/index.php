<?php
/**
 *
 * Copyright (c) 2009, Caybo.ru
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of Caybo.ru. located at
 * www.caybo.ru. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@caybo.ru
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) Caybo.ru, 2009
 * @version $Id$
 *
 */

$startTime = microtime(true);

// system testing function
function bug($var) { echo '<pre>'.htmlspecialchars(print_r($var, true)).'</pre>'; die(); }

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
              realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(
	dirname(dirname(__FILE__)) . '/library',
	get_include_path(),
)));

// Create application, bootstrap, and run
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config/app.ini');
$application->bootstrap()
            ->run();

