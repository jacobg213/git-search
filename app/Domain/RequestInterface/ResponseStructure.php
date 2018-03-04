<?php

namespace App\Domain\RequestInterface;

class ResponseStructure
{
    public $per_page, $page, $sort, $order, $term, $repositories, $total;

    public function __construct(
        int $per_page,
        int $page,
        string $sort,
        string $order,
        string $term,
        array $repositories,
        int $total = null
    ) {
        $this->per_page = $per_page;
        $this->page = $page;
        $this->total = $total;
        $this->sort = $sort;
        $this->order = $order;
        $this->term = $term;
        $this->repositories = $repositories;
    }
}