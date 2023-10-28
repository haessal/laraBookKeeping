<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class BookService
{
    /**
     * Book repository instance.
     *
     * @var \App\DataProvider\BookRepositoryInterface
     */
    private $book;

    /**
     * Permission repository instance.
     *
     * @var \App\DataProvider\PermissionRepositoryInterface
     */
    private $permission;

    /**
     * Create a new BookService instance.
     *
     * @param  \App\DataProvider\BookRepositoryInterface  $book
     * @param  \App\DataProvider\PermissionRepositoryInterface  $permission
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission)
    {
        $this->book = $book;
        $this->permission = $permission;
    }

    /**
     * Create a new book.
     *
     * @param  int  $userId
     * @param  string  $title
     * @return string
     */
    public function createBook($userId, $title)
    {
        $bookId = $this->book->create($title);
        $this->permission->create($userId, $bookId, true, true, false);

        return $bookId;
    }

    /**
     * Create a new permission.
     *
     * @param  string  $bookId
     * @param  string  $userName
     * @param  'ReadWrite'|'ReadOnly'  $mode
     * @return array{user: string, permitted_to: 'ReadWrite'|'ReadOnly'}|null
     */
    public function createPermission($bookId, $userName, $mode): ?array
    {
        $permission = null;
        $user = $this->permission->findUserByName($userName);
        if (isset($user)) {
            $modifiable = ($mode == 'ReadWrite') ? true : false;
            $this->permission->create(intval($user['id']), $bookId, $modifiable, false, false);
            $permission = ['user' => $userName, 'permitted_to' => $mode];
        }

        return $permission;
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
     * Delete the permission that the user access to the book.
     *
     * @param  string  $bookId
     * @param  string  $userName
     * @return void
     */
    public function deletePermission($bookId, $userName)
    {
        $user = $this->permission->findUserByName($userName);
        if (isset($user)) {
            $this->permission->delete(intval($user['id']), $bookId);
        }
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
     * @return array<string, mixed>
     */
    public function importInformation($sourceUrl, $accessToken, $userId, array $bookHead): array
    {
        $bookId = $bookHead['book_id'];
        $mode = null;
        $result = null;
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
            $destinationUpdateDate = null;
            if (isset($destinationInformation['updated_at'])) {
                $dD = Carbon::createFromFormat(Carbon::ATOM, $destinationInformation['updated_at']);
                if (! is_bool($dD)) {
                    $destinationUpdateDate = $dD;
                }
            }
            $sourceUpdateDate = null;
            if (isset($bookHead['updated_at'])) {
                $sD = Carbon::createFromFormat(Carbon::ATOM, $bookHead['updated_at']);
                if (! is_bool($sD)) {
                    $sourceUpdateDate = $sD;
                }
            }
            if (isset($sourceUpdateDate)) {
                if (is_null($destinationUpdateDate) || $sourceUpdateDate->gt($destinationUpdateDate)) {
                    $mode = 'update';
                }
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $response = Http::withToken($accessToken)->get($sourceUrl.'/'.$bookId);
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
                 * } $responseBody
                 */
                $responseBody = $response->json();
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
            }
        } else {
            $result = 'already up-to-date';
        }

        return ['bookId' => $bookId, 'result' => $result];
    }

    /**
     * Retrieve the book if it is available to the user.
     *
     * @param  string  $bookId
     * @param  int  $userId
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   modifiable: bool,
     *   is_owner: bool,
     *   is_default: bool,
     *   created_at: string,
     * }|null
     */
    public function retrieveBook($bookId, $userId): ?array
    {
        $book = $this->permission->findBook($userId, $bookId);

        return is_null($book) ? null : [
            'book_id'    => strval($book['book_id']),
            'book_name'  => strval($book['book_name']),
            'modifiable' => boolval($book['modifiable']),
            'is_owner'   => boolval($book['is_owner']),
            'is_default' => boolval($book['is_default']),
            'created_at' => strval($book['created_at']),
        ];
    }

    /**
     * Retrieve a list of books that is available to the user.
     *
     * @param  int  $userId
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   modifiable: bool,
     *   is_owner: bool,
     *   is_default: bool,
     *   created_at: string,
     * }[]
     */
    public function retrieveBooks($userId): array
    {
        $booklist = [];

        $list = $this->permission->searchForAccessibleBooks($userId);
        foreach ($list as $book) {
            $booklist[] = [
                'book_id'    => strval($book['book_id']),
                'book_name'  => strval($book['book_name']),
                'modifiable' => boolval($book['modifiable']),
                'is_owner'   => boolval($book['is_owner']),
                'is_default' => boolval($book['is_default']),
                'created_at' => strval($book['created_at']),
            ];
        }

        return $booklist;
    }

    /**
     * Retrieve the default book of the user.
     *
     * @param  int  $userId
     * @return string|null
     */
    public function retrieveDefaultBook($userId)
    {
        $bookId = $this->permission->findDefaultBook($userId);

        return $bookId;
    }

    /**
     * Retrieve the default book when the book isn't specified, or
     * check if the specified book is readable.
     *
     * @param  string|null  $bookId
     * @param  int  $userId
     * @return array{0:int, 1:string}
     */
    public function retrieveDefaultBookOrCheckReadable($bookId, $userId)
    {
        if (is_null($bookId)) {
            $bookId = $this->permission->findDefaultBook($userId);
            if (is_null($bookId)) {
                return [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, ''];
            }
        } else {
            [$authorized, $reason] = $this->readable($bookId, $userId);
            if (! $authorized) {
                return [$reason, ''];
            }
        }

        return [BookKeepingService::STATUS_NORMAL, $bookId];
    }

    /**
     * Retrieve the default book when the book isn't specified, or
     * check if the specified book is writable.
     *
     * @param  string|null  $bookId
     * @param  int  $userId
     * @return array{0:int, 1:string}
     */
    public function retrieveDefaultBookOrCheckWritable($bookId, $userId)
    {
        if (is_null($bookId)) {
            $bookId = $this->permission->findDefaultBook($userId);
            if (is_null($bookId)) {
                return [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, ''];
            }
        } else {
            [$authorized, $reason] = $this->writable($bookId, $userId);
            if (! $authorized) {
                return [$reason, ''];
            }
        }

        return [BookKeepingService::STATUS_NORMAL, $bookId];
    }

    /**
     * Retrieve the information of the book.
     *
     * @param  string  $bookId
     * @return array{
     *   book_id: string,
     *   book_name: string,
     * }|null
     */
    public function retrieveInformationOf($bookId): ?array
    {
        $book = $this->book->findById($bookId);

        return is_null($book) ? null : [
            'book_id'   => strval($book['book_id']),
            'book_name' => strval($book['book_name']),
        ];
    }

    /**
     * Retrieve the name of the owner of the book.
     *
     * @param  string  $bookId
     * @return string|null
     */
    public function retrieveOwnerNameOf($bookId)
    {
        $user = $this->permission->findOwnerOfBook($bookId);

        return is_null($user) ? null : $user['name'];
    }

    /**
     * Retrieve a list of permissions related to the book.
     *
     * @param  string  $bookId
     * @return array{user: string, permitted_to: 'ReadWrite'|'ReadOnly'}[]
     */
    public function retrievePermissions($bookId): array
    {
        $permissions = [];
        $permission_list = $this->permission->findByBookId($bookId);
        foreach ($permission_list as $item) {
            $user = $this->permission->findUser(intval($item['permitted_user']));
            if (isset($user)) {
                $permissions[] = [
                    'user' => strval($user['name']),
                    'permitted_to' => ($item['modifiable'] != 0) ? 'ReadWrite' : 'ReadOnly',
                ];
            }
        }

        return $permissions;
    }

    /**
     * Update the mark for indicating that the book is default one for the user.
     *
     * @param  string  $bookId
     * @param  int  $userId
     * @param  bool  $isDefault
     * @return void
     */
    public function updateDefaultMarkOf($bookId, $userId, $isDefault)
    {
        $this->permission->updateDefaultBookMark($userId, $bookId, $isDefault);
    }

    /**
     * Update the name of the book.
     *
     * @param  string  $bookId
     * @param  string  $newName
     * @return void
     */
    public function updateNameOf($bookId, $newName)
    {
        $this->book->updateName($bookId, $newName);
    }

    /**
     * Check if the user can read the book.
     *
     * @param  string  $bookId
     * @param  int  $userId
     * @return array{0:bool, 1:int}
     */
    private function readable($bookId, $userId): array
    {
        $book = $this->permission->findBook($userId, $bookId);
        if (is_null($book)) {
            return [false, BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE];
        }

        return [true, BookKeepingService::STATUS_NORMAL];
    }

    /**
     * Check if the user can write the book.
     *
     * @param  string  $bookId
     * @param  int  $userId
     * @return array{0:bool, 1:int}
     */
    private function writable($bookId, $userId): array
    {
        $book = $this->permission->findBook($userId, $bookId);
        if (is_null($book)) {
            return [false, BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE];
        }
        if (boolval($book['modifiable']) == false) {
            return [false, BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN];
        }

        return [true, BookKeepingService::STATUS_NORMAL];
    }
}
