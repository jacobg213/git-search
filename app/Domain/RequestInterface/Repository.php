<?php

namespace App\Domain\RequestInterface;

class Repository
{
    public $name, $full_name, $description, $author, $rating, $url, $created_at, $updated_at;

    public function __construct(
        string $name,
        string $full_name,
        string $description,
        string $author,
        int $rating,
        string $url,
        string $created_at,
        string $updated_at
    ) {
        $this->name = $name;
        $this->full_name = $full_name;
        $this->description = $description;
        $this->author = $author;
        $this->rating = $rating;
        $this->url = $url;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}