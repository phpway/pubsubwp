<?php
namespace PubSubWP;

class Queue
{
    protected $subcribers = [];
    protected $isSorted   = true;

    /**
     * Add callback to this queue with given optional priority.
     *
     * @param   callable    $callback   callback to add to the queue.
     * @param   int         $priority   optional, callback priority in the queue
     * @return  Queue
     */
    public function add(callable $callback, int $priority = 0)
    {
        $this->subscribers[] = [
            'callback' => $callback,
            'priority' => $priority
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
                $rankA = $a['priority'];
                $rankB = $b['priority'];
                return $rankA === $rankB ? 0 : ($rankA < $rankB ? 1 : -1);
            }
        );
        $this->isSorted = true;
    }
}
