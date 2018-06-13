<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
declare (strict_types=1);

use PHPUnit\Framework\TestCase;

use Blossom\Classes\Template;

class ControllerTest extends TestCase
{
    const CONTROLLER_NS = '\Application\Controllers';
    public function controllerProvider()
    {
        $skip = ['.', '..', 'LoginController'];
        $controllers = [];

        $dir   = '../../src/Application/Controllers';
        $files = scandir($dir);
        foreach ($files as $f) {
            if (!in_array($f, $skip)) {
                $controller    = substr($f, 0, -4);
                $controllers[] = [self::CONTROLLER_NS."\\$controller"];
            }
        }
        return $controllers;
    }

    /**
     * @dataProvider controllerProvider
     * @runInSeparateProcess
     */
    public function testControllersReturnTemplates(string $classname)
    {
        $template   = new Template('default', 'html');
        $controller = new $classname($template);
        $functions  = get_class_methods($controller);
        foreach ($functions as $f) {
            if ($f != '__construct' && $f != 'generateUrl') {
                $t = $controller->$f();
                $this->assertInstanceOf(Template::class, $t, "$classname::$f did not return a template");
            }
        }
    }
}
