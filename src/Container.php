<?php

namespace PayKassa;

abstract class Container extends BaseRequest
{
    public array $items = [];

    /**
     * @param object $item
     */
    public function add(object $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        $request = [];
        foreach ($this->items as $item) {
            $request[] = $item->makeRequest();
        }

        return $request;
    }
}
