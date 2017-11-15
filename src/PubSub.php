<?php
namespace PubSubWP;

/**
 * Provides simple publish-subscribe mechanism with 2 additional features:
 *  1. topic subscribers can specify priority in which they will be called
 *     when topic is published
 *  2. topic subscriber has an option to stop propagating the event through
 *     out the remaining subscribers
 *
 *  Example of use:
 *    $pubsub = new PubSubWP;
 *    $pubsub::subscribe('foo', function ($event) { $event['data'] .= 'a'}, 1);
 *    $pubsub::subscribe('foo', function ($event) { $event['data'] .= 'b'; $event->stop(); }, 2);
 *    $pubsub::subscribe('foo', function ($event) { $event['data'] .= 'c'}, 3);
 *    $event = $pubsub->publish('foo', ['data'] => '');
 *    print $event['data'];  // -> 'cb'
 */
class PubSub
{
    protected $queue = [];

    /**
     * Subscribe given callback to the given topic. Optionally set the priority
     * in which the callbacks will be called (higher priority first).
     *
     * @param   string      $topic      topic to subscribe to
     * @param   callable    $callback   callback to call when the topic is published
     *                                  the callback receives Event object as a single
     *                                  parameter
     * @param   int         $priority   optional, priority for the callback in the queue
     */
    public function subscribe($topic, callable $callback, int $priority = 0)
    {
        if (!isset($this->queue[$topic])) {
            $this->queue[$topic] = new Queue;
        }
        $this->queue[$topic]->add($callback, $priority);
    }

    /**
     * Publish given topic. This method will return an Event object that was
     * passed through all callbacks subscribed to this topic in order of their
     * priority. Subscriber callback can however call stop() method of the Event
     * to skip processing it through all remaining callbacks.
     *
     * @param   string          $topic      topic to publish
     * @param   array|Event     $event      optional, event object to pass to
     *                                      topic subscribers or initial data to
     *                                      set on that event
     * @return  Event                       event object processed through all
     *                                      callbacks subscribed to the topic
     * @throws  \InvalidArgumentException   if there are no subscribers to the
     *                                      given topic
     */
    public function publish($topic, $event = [])
    {
        if (!isset($this->queue[$topic])) {
            throw new \InvalidArgumentException("Topic '$topic' doesn't have any subscribers.");
        }

        if (!$event instanceof Event && !is_array($event)) {
            throw new \InvalidArgumentException("Second parameter must be instance of Event or array.");
        }

        // create an event (with optional initial data) to pass to subscribers
        $event = $event instanceof Event ? $event : new Event($event);

        // pass the event through all callbacks subscribed to the given topic
        $queue = $this->queue[$topic];
        foreach ($queue->get() as $callback) {
            call_user_func($callback, $event);

            // subscribers can request stop propagating the event; in such a case
            // exit the loop, otherwise pass the event to next subscriber
            if ($event->isStopped()) {
                break;
            }
        }

        return $event;
    }
}
