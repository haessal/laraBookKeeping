<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\User;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Create a permission for the user to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string
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
     * Delete the user's permission to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return void
     */
    public function delete(int $userId, string $bookId): void
    {
        $permission = Permission::where('permitted_user', $userId)
            ->where('readable_book', $bookId)
            ->first();
        if (! is_null($permission)) {
            $permission->forceDelete();
        }
    }

    /**
     * Find the books that the user can access.
     *
     * @param  int  $userId
     * @param  string|null  $bookId
     * @return array<int, array<string, string>>
     */
    public function findAccessibleBooks(int $userId, string $bookId = null): array
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
     * Find the permission to access the book.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function findByBookId(string $bookId): array
    {
        $list = Permission::select('permitted_user', 'modifiable', 'is_owner', 'is_default')
            ->where('readable_book', $bookId)
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
     * @return array<string, string>|null
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
     * Find the user.
     *
     * @param  int  $userId
     * @return array<string, string>|null
     */
    public function findUser(int $userId): ?array
    {
        $user = User::find($userId);

        return is_null($user) ? null : $user->toArray();
    }

    /**
     * Find the user by his/her name.
     *
     * @param  string  $name
     * @return array<string, string>|null
     */
    public function findUserByName(string $name): ?array
    {
        $user = User::where('name', $name)->first();

        return is_null($user) ? null : $user->toArray();
    }

    /**
     * Update the mark for indicating that the book is default one for the user.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $isDefault
     * @return void
     */
    public function updateDefaultBookMark(int $userId, string $bookId, bool $isDefault): void
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
}
