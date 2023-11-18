<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;

class BookMigrationService extends BookService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new BookMigrationService instance.
     *
     * @param  \App\DataProvider\BookRepositoryInterface  $book
     * @param  \App\DataProvider\PermissionRepositoryInterface  $permission
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission, BookKeepingMigrationTools $tools)
    {
        parent::__construct($book, $permission);
        $this->tools = $tools;
    }

    /**
     * Export information.
     *
     * @param  string  $bookId
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function exportInformation(string $bookId): ?array
    {
        /** @var array{
         *   book_id: string,
         *   book_name: string,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }|null $book
         */
        $book = $this->book->findByIdForExporting($bookId);
        if (isset($book)) {
            $converted = [
                'book_id'       => $book['book_id'],
                'book_name'     => $book['book_name'],
                'display_order' => $book['display_order'],
                'updated_at'    => $book['updated_at'],
                'deleted'       => ! is_null($book['deleted_at']),
            ];
        } else {
            $converted = null;
        }

        return $converted;
    }

    /**
     * Import information.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  int  $userId
     * @param  array{
     *   book_id: string,
     *   updated_at: string|null,
     * }  $bookHead
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function importInformation($sourceUrl, $accessToken, $userId, array $bookHead): array
    {
        $bookId = $bookHead['book_id'];
        $mode = null;
        $result = null;
        $error = null;

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
            $sourceUpdateAt = $bookHead['updated_at'];
            $destinationUpdateAt = $destinationInformation['updated_at'];
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $url = $sourceUrl.'/'.$bookId;
            $response = $this->tools->getFromExporter($url, $accessToken);
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     book: array{
                 *       book_id: string,
                 *       book_name: string,
                 *       display_order: int|null,
                 *       updated_at: string|null,
                 *       deleted: bool,
                 *     },
                 *   }>,
                 * }|null $responseBody
                 */
                $responseBody = $response->json();
                if (isset($responseBody)) {
                    $book = $responseBody['books'][$bookId]['book'];
                    switch($mode) {
                        case 'update':
                            $this->book->updateForImporting($book);
                            $result = 'updated';
                            break;
                        case 'create':
                            $this->book->createForImporting($book);
                            $this->permission->create($userId, $bookId, true, true, false);
                            $result = 'created';
                            break;
                        default:
                            break;
                    }
                } else {
                    $error = 'No response data. '.$url;
                }
            } else {
                $error = 'Response error('.$response->status().'). '.$url;
            }
        } else {
            $result = 'already up-to-date';
        }

        return [['bookId' => $bookId, 'result' => $result], $error];
    }

    /**
     * Load information.
     *
     * @param  int  $userId
     * @param  array<string, mixed>  $bookInformation
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public function loadInformation($userId, $bookInformation): array
    {
        $mode = null;
        $result = null;
        $error = null;

        $newBook = $this->validateBookInformation($bookInformation);
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

    /**
     * Validate the book information.
     *
     * @param  array<string, mixed>  $bookInformation
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    private function validateBookInformation(array $bookInformation): ?array
    {
        if (! key_exists('book_id', $bookInformation) || ! is_string($bookInformation['book_id'])) {
            return null;
        }
        if (! key_exists('book_name', $bookInformation) || ! is_string($bookInformation['book_name'])) {
            return null;
        }
        if (! key_exists('display_order', $bookInformation) ||
                (! is_int($bookInformation['display_order']) && ! is_null($bookInformation['display_order']))) {
            return null;
        }
        if (! key_exists('updated_at', $bookInformation) ||
                (! is_string($bookInformation['updated_at']) && ! is_null($bookInformation['updated_at']))) {
            return null;
        }
        if (! key_exists('deleted', $bookInformation) || ! is_bool($bookInformation['deleted'])) {
            return null;
        }

        return [
            'book_id'       => $bookInformation['book_id'],
            'book_name'     => $bookInformation['book_name'],
            'display_order' => $bookInformation['display_order'],
            'updated_at'    => $bookInformation['updated_at'],
            'deleted'       => $bookInformation['deleted'],
        ];
    }
}
