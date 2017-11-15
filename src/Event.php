<?php
namespace PubSubWP;

class Event extends \ArrayObject
{
    protected $isStopped = false;

    /**
     * Flag this event as stopped.
     *
     * @return  Event
     */
    public function stop()
    {
        $this->isStopped = true;
        return $this;
    }

    /**
     * Check whether this event is flagged as stopped.
     *
     * @return  boolean     true if this event is flagged as stopped,
     *                      false otherwise
     */
    public function isStopped()
    {
        return $this->isStopped;
    }
}
