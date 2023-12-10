<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;

class BookMigrationLoaderService extends BookMigrationService
{
    /**
     * Validator for loading.
     *
     * @var \App\Service\BookKeepingMigrationValidator
     */
    private $validator;

    /**
     * Create a new BookMigrationService instance.
     *
     * @param  \App\DataProvider\BookRepositoryInterface  $book
     * @param  \App\DataProvider\PermissionRepositoryInterface  $permission
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     * @param  \App\Service\BookKeepingMigrationValidator  $validator
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission, BookKeepingMigrationTools $tools, BookKeepingMigrationValidator $validator)
    {
        parent::__construct($book, $permission, $tools);
        $this->validator = $validator;
    }

    /**
     * Load information.
     *
     * @param  int  $userId
     * @param  array<string, mixed>  $bookInformation
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadInformation($userId, array $bookInformation): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newBook = $this->validator->validateBookInformation($bookInformation);
        if (is_null($newBook)) {
            $error = 'invalid data format: book';

            return [['bookId' => null, 'result' => $result], $error];
        }
        $bookId = $newBook['book_id'];

        /** @var array{
         *   book_id: string,
         *   book_name: string,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }|null $destinationInformation
         */
        $destinationInformation = $this->book->findByIdForExporting($bookId);
        if (isset($destinationInformation)) {
            $sourceUpdateAt = $newBook['updated_at'];
            $destinationUpdateAt = $destinationInformation['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            switch($mode) {
                case 'update':
                    $this->book->updateForImporting($newBook);
                    $result = 'updated';
                    break;
                case 'create':
                    $this->book->createForImporting($newBook);
                    $this->permission->create($userId, $bookId, true, true, false);
                    $result = 'created';
                    break;
                default:
                    break;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['bookId' => $bookId, 'result' => $result], $error];
    }
}
