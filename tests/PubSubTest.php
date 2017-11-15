<?php
namespace PubSubWP\Tests;

use PubSubWP\Event;
use PubSubWP\PubSub;

class PubSubTest extends \PHPUnit_Framework_TestCase
{
    public function testPubSub()
    {
        $pubsub = new PubSub;

        // subscribe few events that will write to the tape
        $writer1 = function ($event) {
            $event['tape'] .= '1';
        };
        $writer2 = function ($event) {
            $event['tape'] .= '2';
        };
        $writer3 = function ($event) {
            $event['tape'] .= 's';
            $event->stop();
        };

        $pubsub->subscribe('foo', $writer1);
        $pubsub->subscribe('foo', $writer2);
        $pubsub->subscribe('foo.priority', $writer1);
        $pubsub->subscribe('foo.priority', $writer2, 1);
        $pubsub->subscribe('foo.stop', $writer1);
        $pubsub->subscribe('foo.stop', $writer2, 10);
        $pubsub->subscribe('foo.stop', $writer1);
        $pubsub->subscribe('foo.stop', $writer1, -10);
        $pubsub->subscribe('foo.stop', $writer3);
        $pubsub->subscribe('foo.stop', $writer1);
        $pubsub->subscribe('foo.stop', $writer2);

        $event = $pubsub->publish('foo', ['tape' => 't']);
        $this->assertSame('t12', $event['tape']);

        $event = $pubsub->publish('foo.priority', ['tape' => 't']);
        $this->assertSame('t21', $event['tape']);

        $event = $pubsub->publish('foo.stop', ['tape' => 't']);
        $this->assertSame('t211s', $event['tape']);
    }

    public function testPublishWithNoEventData()
    {
        $pubsub = new PubSub;
        $capture = new \stdClass;
        $pubsub->subscribe(
            'topic',
            function ($event) use ($capture) {
                $capture->event = $event;
                $event['fun1']  = true;
            }
        );

        $event = $pubsub->publish('topic');

        $this->assertTrue($event instanceof Event);
        $this->assertSame($event, $capture->event);
        $this->assertTrue($event['fun1']);
    }
}
