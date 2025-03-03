<?php

namespace PayKassa\Model\Check;

use PayKassa\Container;

class CheckItems extends Container
{
    /**
     * @param object $item
     *
     * @return void
     */
    public function add(object $item): void
    {
        parent::add($item);
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return new ItemDetails();
    }
}
