<?php
namespace PubSubWP\Tests;

use PubSubWP\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $event = new Event;
        $this->assertTrue($event instanceof \ArrayObject);
        $this->assertSame([], $event->getArrayCopy());
    }

    public function testStopped()
    {
        $event = new Event;
        $this->assertFalse($event->isStopped());
        $event->stop();
        $this->assertTrue($event->isStopped());
    }
}
