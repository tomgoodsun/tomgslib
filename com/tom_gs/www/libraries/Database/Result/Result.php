<?php
/**
 * Database result
 */

namespace com\tom_gs\www\libraries\Database\Result;

class Result extends \ArrayObject implements ResultHandlable
{
    public $current_page = 0;
    public $prev_page = 0;
    public $next_page = 0;
    public $item_per_page = 0;
    public $total_items = 0;
    public $first_page = 0;
    public $last_page = 0;
    public $total_pages = 0;

    public function __construct(
        $input,
        $page = null,
        $item_per_page = null,
        $total_items = null
    ) {
        if ($page !== null && $item_per_page !== null && $total_items !== null) {
            $page = intval($page) <= 0 ? 1 : intval($page);
            $this->current_page = $page;
            $this->next_page = $page + 1;
            $this->prev_page = ($page - 1) <= 0 ? 0 : $page - 1;
            $this->item_per_page = intval($item_per_page);
            $this->total_items = intval($total_items);
            $this->first_page = 1;
            $this->last_page =
                floor($this->total_items / $this->item_per_page) + 1;
            $this->total_pages =
                floor($this->total_items / $this->item_per_page) + 1;
        }
        parent::__construct($input);
    }

    public function getResult()
    {
        return $this->getArrayCopy();
    }
}
