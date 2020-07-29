<?php

use Teraone\Twig\Extension\StrftimeExtension;

class Twig_Tests_Extension_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_Environment
     */
    private $env;

    public static function setUpBeforeClass()
    {
        if (!class_exists('Teraone\Twig\Extension\StrftimeExtension')) {
            self::markTestSkipped('Unable to find class StrftimeExtension.');
        }
    }
    public function setUp()
    {
        $timezone = new DateTimeZone(date_default_timezone_get());
        $coreExtension = $this->getMock('Twig_Extension_Core');
        $coreExtension
            ->expects($this->any())
            ->method('getTimezone')
            ->will($this->returnValue($timezone));
        $this->env = $this->getMock('Twig_Environment');
        $this->env
            ->expects($this->any())
            ->method('getExtension')
            ->with('core')
            ->will($this->returnValue($coreExtension))
        ;
    }



    public function testStrftimeIsIdentical()
    {
        $format = '%a %A %b %B %c %C %d %d %D %e %g %G %h %h %I %j %m %M %n %p %r %R %S %t %T %u' ;
        $extension = new StrftimeExtension();
        $this->assertEquals(strftime($format), $extension->strftime($this->env, new DateTime(), $format));
    }

}