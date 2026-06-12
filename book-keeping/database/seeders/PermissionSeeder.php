<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = User::where('name', 'admin')->value('id');
        $testerId = User::where('name', 'tester')->value('id');
        $books = Book::all();

        $index = 0;
        foreach ($books as $book) {
            if ($index > 3) {
                break;
            }
            switch ($index) {
                case 0:
                    Permission::factory()->create([
                        'permitted_user' => $adminId,
                        'readable_book' => $book->book_id,
                        'modifiable' => true,
                        'is_owner' => true,
                        'is_default' => true,
                    ]);
                    Permission::factory()->create([
                        'permitted_user' => $testerId,
                        'readable_book' => $book->book_id,
                        'modifiable' => false,
                        'is_owner' => false,
                        'is_default' => false,
                    ]);
                    break;
                case 1:
                    Permission::factory()->create([
                        'permitted_user' => $adminId,
                        'readable_book' => $book->book_id,
                        'modifiable' => true,
                        'is_owner' => true,
                        'is_default' => false,
                    ]);
                    Permission::factory()->create([
                        'permitted_user' => $testerId,
                        'readable_book' => $book->book_id,
                        'modifiable' => true,
                        'is_owner' => false,
                        'is_default' => false,
                    ]);
                    break;
                case 2:
                    Permission::factory()->create([
                        'permitted_user' => $testerId,
                        'readable_book' => $book->book_id,
                        'modifiable' => true,
                        'is_owner' => true,
                        'is_default' => false,
                    ]);
                    break;
                case 3:
                    Permission::factory()->create([
                        'permitted_user' => $testerId,
                        'readable_book' => $book->book_id,
                        'modifiable' => true,
                        'is_owner' => true,
                        'is_default' => true,
                    ]);
                    break;
                default:
                    break;
            }
            $index++;
        }
    }
}
