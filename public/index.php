<?php
/**
 * @copyright 2012-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$startTime = microtime(1);

include '../bootstrap.inc';
use Blossom\Classes\Template;
use Blossom\Classes\Block;

ini_set('session.save_path', SITE_HOME.'/sessions');
ini_set('session.cookie_path', BASE_URI);
session_start();

// Check for routes
if (preg_match('|'.BASE_URI.'(/([a-zA-Z0-9]+))?(/([a-zA-Z0-9]+))?|',$_SERVER['REQUEST_URI'],$matches)) {
	$resource = isset($matches[2]) ? $matches[2] : 'index';
	$action   = isset($matches[4]) ? $matches[4] : 'index';
}

// Create the default Template
$template = !empty($_REQUEST['format'])
	? new Template('default',$_REQUEST['format'])
	: new Template('default');

// Execute the Controller::action()
if (isset($resource) && isset($action) && $ZEND_ACL->hasResource($resource)) {
	$USER_ROLE = isset($_SESSION['USER']) ? $_SESSION['USER']->role : 'Anonymous';
	if ($ZEND_ACL->isAllowed($USER_ROLE, $resource, $action)) {
		$controller = 'Application\Controllers\\'.ucfirst($resource).'Controller';
        if (method_exists($controller, $action)) {
            $c = new $controller($template);
            $template = $c->$action();
        }
        else {
            $template = new \Application\Views\NotFoundView();
        }
	}
	else {
        $template = new \Application\Views\ForbiddenView();
	}
}
else {
    $template = new \Application\Views\NotFoundView();
}

echo $template->render();

# Calculate the process time
if ($template->outputFormat == 'html') {
    $endTime = microtime(1);
    $processTime = $endTime - $startTime;
    echo "<!-- Process Time: $processTime -->";
}
