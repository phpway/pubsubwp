# Pubsub with priority

[![Build Status](https://travis-ci.org/phpway/pubsubwp.svg?branch=master)](https://travis-ci.org/phpway/pubsubwp)

I hear you - why another pubsub library when there is already a plethora of them?
I was looking for a simple PHP publish-subscribe implementation with following
properties:
 * simple and easy to use
 * ability to set optional priority for subscribers; those with higher priority
   will be called first when topic is published
 * ability for subscribers to stop calling following callbacks in the queue when
   topic is published

This simple pubsub library tries to fill in this gap.

## Example of usage

Create pubsub instance:

```php
$pubsub = new \PubSubWP\PubSub;
```

Subscribe few callbacks to some topic:

```php
$pubsub->subscribe('topic.foo', function ($event) { $event['tape'] .= 'a'; });
$pubsub->subscribe('topic.foo', function ($event) { $event['tape'] .= 'b'; });
```

You can also subscribe callback with priority. When topic is published, callbacks
are sorted by this priority before they are called (higher priority callbacks
will be executed first). Default priority is 0.

```php
$priority = 10;
$pubsub->subscribe('topic.foo', function ($event) { $event['tape'] .= 'A'; }, $priority);
```

When topic is published via `PubSub::publish()` method, the pubsub will create
an event object (inherited from `ArrayObject`) and it will start passing it
through all subscribed callbacks sorted by priorities:

```php
$initialEventData = ['tape' => ''];
$event = $pubsub->publish('topic.foo', $initialEventData);
```

Event's initial data can be specified in second optional parameter. Each subscribed
callback will receive this event object in the (only) parameter.

```php
print $event['tape'];   // 'Aab'
```

Callback writing 'A' on tape was executed first since it has higher priority
than others.

Callback can also decide to skip all other callbacks in the queue that have not
been executed yet. This is done simply via calling `Event::stop()` method:
```php
$pubsub->subscribe(
    'topic.foo',
    function ($event) {
        $event['tape'] .= '[STOP]';
        $event->stop();
    },
    5
);
```

This will cause skipping all callbacks with priority lower than 5:
```php
$event = $pubsub->publish('topic.foo', ['tape' => '']);
print $event['tape'];   // 'A[STOP]'
```

You can also specify your own event that will be passed through the subscribed
callbacks. The only requirement is that it must inherit from `PubSubWP\Event`
which is the class of the default event:

```php
$myEvent = new MyEvent;
$event = $pubsub->publish('topic.foo', $myEvent);
```
