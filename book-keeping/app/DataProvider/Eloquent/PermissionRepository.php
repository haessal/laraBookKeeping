<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\User;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Create a new permission for the user to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string
     */
    public function create($userId, $bookId, $modifiable, $is_owner, $is_default)
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
    public function delete($userId, $bookId)
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $permission */
        $permission = Permission::query()
            ->where('permitted_user', $userId)
            ->where('readable_book', $bookId)
            ->first();
        if (! is_null($permission)) {
            $permission->forceDelete();
        }
    }

    /**
     * Find the book that the user can access.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findBook($userId, $bookId): ?array
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $book */
        $book = Permission::query()
            ->select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->where('book_id', $bookId)
            ->first();

        return is_null($book) ? null : $book->toArray();
    }

    /**
     * Find the permission to access the book.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function findByBookId($bookId): array
    {
        /** @var array<int, array<string, mixed>> $list */
        $list = Permission::query()
            ->select('permitted_user', 'modifiable', 'is_owner', 'is_default')
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
    public function findDefaultBook($userId)
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $book */
        $book = Permission::query()
            ->select('book_id')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->where('is_default', true)
            ->first();
        if (! is_null($book)) {
            $book_in_array = $book->toArray();
            if (array_key_exists('book_id', $book_in_array)) {
                return $book_in_array['book_id'];
            }
        }

        return null;
    }

    /**
     * Find the owner of the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findOwnerOfBook($bookId): ?array
    {
        $owner = null;
        /** @var \Illuminate\Database\Eloquent\Model|null $permission */
        $permission = Permission::query()
            ->select('permitted_user')
            ->where('readable_book', $bookId)
            ->where('is_owner', true)
            ->first();
        if (! is_null($permission)) {
            $permission_in_array = $permission->toArray();
            if (array_key_exists('permitted_user', $permission_in_array)) {
                /** @var \App\Models\User|null $user */
                $user = User::query()->find($permission_in_array['permitted_user']);
                $owner = is_null($user) ? null : $user->toArray();
            }
        }

        return $owner;
    }

    /**
     * Find the user.
     *
     * @param  int  $userId
     * @return array<string, mixed>|null
     */
    public function findUser($userId): ?array
    {
        /** @var \App\Models\User|null $user */
        $user = User::query()->find($userId);

        return is_null($user) ? null : $user->toArray();
    }

    /**
     * Find the user by his/her name.
     *
     * @param  string  $name
     * @return array<string, mixed>|null
     */
    public function findUserByName($name): ?array
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $user */
        $user = User::query()->where('name', $name)->first();

        return is_null($user) ? null : $user->toArray();
    }

    /**
     * Search for the available books.
     *
     * @param  int  $userId
     * @return array<int, array<string, string>>
     */
    public function searchForAccessibleBooks($userId): array
    {
        /** @var array<int, array<string, string>> $list */
        $list = Permission::query()
            ->select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->orderBy('bk2_0_books.created_at')
            ->get()->toArray();

        return $list;
    }

    /**
     * Update the mark for indicating that the book is default one for the user.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $isDefault
     * @return void
     */
    public function updateDefaultBookMark($userId, $bookId, $isDefault)
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $selected */
        $selected = Permission::query()
            ->select('permission_id')
            ->where('permitted_user', $userId)
            ->where('readable_book', $bookId)
            ->where('is_owner', true)
            ->first();
        if (! is_null($selected)) {
            $selected_in_array = $selected->toArray();
            if (array_key_exists('permission_id', $selected_in_array)) {
                /** @var \App\Models\Permission|null $permission */
                $permission = Permission::query()->find($selected_in_array['permission_id']);
                if (! is_null($permission)) {
                    $permission->is_default = $isDefault;
                    $permission->save();
                }
            }
        }
    }
}
