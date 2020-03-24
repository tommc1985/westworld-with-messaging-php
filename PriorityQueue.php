<?php

class PriorityQueue {
    public static $instance;

    protected $items;

    /**
     * Constructor
     * @return [type] [description]
     */
    public function __constuct () {
        $this->items = [];
    }

    /**
     * Get Instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Insert new telegram
     * @param  Telegram $telegram
     */
    public function insert(Telegram $telegram)
    {
        $this->items[] = $telegram;
        $this->reprioritiseItems();
    }

    /**
     * Reprioritise items (usually after an insert)
     */
    public function reprioritiseItems()
    {
        usort($this->items, function ($a, $b) {
            if ($a->getDispatchTime() == $b->getDispatchTime()) {
                return 0;
            }

            return $a->getDispatchTime() < $b->getDispatchTime() ? -1 : 1;
        });
    }

    /**
     * Is Empty
     * @return boolean
     */
    public function isEmpty()
    {
        return !is_array($this->items) || count($this->items) == 0;
    }

    /**
     * Begin
     */
    public function begin()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return reset($this->items);
    }

    public function shift()
    {
        return array_shift($this->items);
    }
}