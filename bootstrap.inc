<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', __DIR__);

/**
 * Multi-Site support
 *
 * To allow multiple sites to use this same install base,
 * define the SITE_HOME variable in the Apache config for each
 * site you want to host.
 *
 * SITE_HOME is the directory where all site-specific data and
 * configuration are stored.  For backup purposes, backing up this
 * directory would be sufficient for an easy full restore.
 */
define('SITE_HOME', !empty($_SERVER['SITE_HOME']) ? $_SERVER['SITE_HOME'] : __DIR__.'/data');
include SITE_HOME.'/site_config.inc';

//-------------------------------------------------------------------
// Bootstrap code
// No editing is usually needed after this point
//-------------------------------------------------------------------
/**
 * Enable autoloading for the PHP libraries
 */
$loader = require APPLICATION_HOME.'/vendor/autoload.php';
$loader->addPsr4('Site\\', SITE_HOME);

include APPLICATION_HOME.'/access_control.inc';

/**
 * Skidder is a web service for error loggin.  This application supports
 * logging errors to a Skidder server.  You must register for an application_id
 * on the skidder server you want to post errors to.
 */
//$writer = new Blossom\Classes\Log\SkidderWriter('http://localhost/skidder/index', 100 );
//$logger = new Zend\Log\Logger();
//$logger->addWriter($writer);
//Zend\Log\Logger::registerErrorHandler($logger);
//Zend\Log\Logger::registerExceptionHandler($logger);
//Zend\Log\Logger::registerFatalErrorShutdownFunction($logger);
