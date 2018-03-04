<?php

namespace App\Domain\RequestInterface;

class ResponseStructure
{
    public $per_page, $page, $sort, $order, $term, $repositories, $total;

    /**
     * ResponseStructure constructor.
     * @param int $per_page
     * @param int $page
     * @param string $sort
     * @param string $order
     * @param string $term
     * @param array $repositories
     * @param int|null $total
     */
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