<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\User;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Create a new permission for the user to modify the book.
     * If the permission is the first one for the user, the book is mark as the
     * default book for the user. And If no user has permission to access the
     * book yet, the user is registered as the owner of the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return string
     */
    public function create(int $userId, string $bookId): string
    {
        $is_default = $this->isRegisteredUser($userId) ? false : true;
        $is_owner = $this->isRegisteredBook($bookId) ? false : true;
        $permission = new Permission();
        $permission->permitted_user = $userId;
        $permission->readable_book = $bookId;
        $permission->modifiable = true;
        $permission->is_owner = $is_owner;
        $permission->is_default = $is_default;
        $permission->save();

        return $permission->permission_id;
    }

    /**
     * Find the books that the user can access.
     *
     * @param  int  $userId
     * @return array
     */
    public function findAccessibleBooks(int $userId): array
    {
        $list = Permission::select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->orderBy('bk2_0_books.created_at')
            ->get()->toArray();

        return $list;
    }

    /**
     * Find the default book of the user.
     *
     * @param  int  $userId
     * @return string|null
     */
    public function findDefaultBook(int $userId): ?string
    {
        $list = Permission::select('book_id')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->where('is_default', true)
            ->first();

        return empty($list) ? null : $list['book_id'];
    }

    /**
     * Find the owner of the book.
     *
     * @param  string  $bookId
     * @return array|null
     */
    public function findOwnerOfBook(string $bookId): ?array
    {
        $owner = null;
        $list = Permission::select('permitted_user')
            ->where('readable_book', $bookId)
            ->where('is_owner', true)
            ->first();
        if (! empty($list)) {
            $user = User::find($list['permitted_user']);
            $owner = is_null($user) ? null : $user->toArray();
        }

        return $owner;
    }

    /**
     * Check if the book is registered.
     *
     * @param  string  $bookId
     * @return bool
     */
    private function isRegisteredBook(string $bookId): bool
    {
        $count = Permission::where('readable_book', $bookId)->count();

        return $count == 0 ? false : true;
    }

    /**
     * Check if the user is registered.
     *
     * @param  int  $userId
     * @return bool
     */
    private function isRegisteredUser(int $userId): bool
    {
        $count = Permission::where('permitted_user', $userId)->count();

        return $count == 0 ? false : true;
    }
}
