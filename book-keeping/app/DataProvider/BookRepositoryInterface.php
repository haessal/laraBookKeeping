<?php

namespace App\DataProvider;

interface BookRepositoryInterface
{
    /**
     * Create new book and register the user as its owner.
     *
     * @param string $title
     *
     * @return string $bookId
     */
    public function create(string $title): string;
}
