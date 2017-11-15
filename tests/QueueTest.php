<?php
namespace PubSubWP\Tests;

use PubSubWP\Queue;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    public function testQueue()
    {
        $queue = new Queue;
        $this->assertSame([], $queue->get());

        $fun1 = function () {
        };
        $fun2 = function () {
        };

        $queue->add($fun1, 2);
        $callbacks = $queue->get();
        $this->assertSame(1, count($callbacks));
        $this->assertSame($fun1, $callbacks[0]);

        $queue->add($fun2, 3);
        $callbacks = $queue->get();
        $this->assertSame(2, count($callbacks));
        $this->assertSame($fun2, $callbacks[0]);
        $this->assertSame($fun1, $callbacks[1]);
    }
}
