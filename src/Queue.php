<?php
namespace PubSubWP;

class Queue
{
    protected $subscribers = [];
    protected $isSorted    = true;

    /**
     * Add callback to this queue with given optional priority.
     *
     * @param   callable    $callback   callback to add to the queue.
     * @param   int         $priority   optional, callback priority in the queue
     * @return  Queue
     */
    public function add(callable $callback, $priority = 0)
    {
        $this->subscribers[] = [
            'callback' => $callback,
            'priority' => (int) $priority,
            'addOrder' => count($this->subscribers)
        ];
        $this->isSorted = false;
        return $this;
    }

    /**
     * Get all callbacks of this queue sorted by priority (higher priority first).
     *
     * @return  array   list of callbacks sorted by priority
     */
    public function get()
    {
        $this->sort();
        return array_map(
            function ($subscriber) {
                return $subscriber['callback'];
            },
            $this->subscribers ?: []
        );
    }

    /**
     * Sort callbacks in this queue (if not already sorted).
     */
    protected function sort()
    {
        if ($this->isSorted) {
            return;
        }

        usort(
            $this->subscribers,
            function ($a, $b) {
                // sort by priority (in reverse order)
                // if priority is same, then by addOrder to preserve the order
                // in which the callbacks were added
                return $a['priority'] === $b['priority']
                    ? $a['addOrder'] - $b['addOrder']
                    : $b['priority'] - $a['priority'];
            }
        );
        $this->isSorted = true;
    }
}
