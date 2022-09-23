<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\User;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Create new permission.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string $permissionId
     */
    public function create(int $userId, string $bookId, bool $modifiable, bool $is_owner, bool $is_default): string
    {
        $permission = new Permission();
        $permission->permitted_user = $userId;
        $permission->readable_book = $bookId;
        $permission->modifiable = $modifiable;
        $permission->is_owner = $is_owner;
        $permission->is_default = $is_default;
        $permission->save();

        return $permission->permission_id;
    }

    /**
     * Find default book of the user.
     *
     * @param  int  $userId
     * @return string | null
     */
    public function findDefaultBook(int $userId)
    {
        $list = Permission::select('book_id')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->where('is_default', true)
            ->first();

        return empty($list) ? null : $list['book_id'];
    }

    /**
     * Find owner of the book.
     *
     * @param  string  $bookId
     * @return array | null
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
     * Search book list that the user can access.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array
     */
    public function searchBookList(int $userId, string $bookId = null): array
    {
        if (! empty($bookId)) {
            $list = Permission::select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
                ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
                ->where('permitted_user', $userId)
                ->where('book_id', $bookId)
                ->orderBy('bk2_0_books.created_at')
                ->get()->toArray();
        } else {
            $list = Permission::select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
                ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
                ->where('permitted_user', $userId)
                ->orderBy('bk2_0_books.created_at')
                ->get()->toArray();
        }

        return $list;
    }

    /**
     * Update the flag which indicates that the book is default one.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  int  $isDefault
     */
    public function updateBookIsDefault(int $userId, string $bookId, bool $isDefault)
    {
        $selected = Permission::select('permission_id')
            ->where('permitted_user', $userId)
            ->where('readable_book', $bookId)
            ->where('is_owner', true)
            ->first();
        if (! empty($selected)) {
            $permission = Permission::find($selected['permission_id']);
            $permission->is_default = $isDefault;
            $permission->save();
        }
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
