<?php

namespace Tests\Unit;

use Tests\TestCase;

abstract class DataProvider_BookRepositoryInterfaceTest extends TestCase
{
    protected $book;

    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertTrue(is_string($bookId));
    }

    /**
     * @test
     */
    public function findById_ReturnValueTypeIsArrayOrNull()
    {
        $bookId = '3274cc74-99a1-47f4-aa57-66da432f5dad';

        $book = $this->book->findById($bookId);

        if (is_null($book)) {
            $this->assertTrue(is_null($book));
        } else {
            $this->assertTrue(is_array($book));
        }
    }
}
