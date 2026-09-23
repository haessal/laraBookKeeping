import DashBoardBookList from '@/Components/Dashboard/DashboardBookList';
import type { Book } from '@/types/BookKeeping/v2/Book';
import { Link } from '@inertiajs/react';
import { Button } from 'flowbite-react';
import { GoRepo } from 'react-icons/go';

export default function DashBoardSideMenu({ book_list }: { book_list: Book[] | null }) {
    return (
        <div className="px-5 pt-6">
            {book_list ? (
                <>
                    <div className="mb-2 flex items-center justify-between">
                        <h3 className="text-base font-semibold text-black dark:text-[#f0f6fc]">Books</h3>
                        <Button
                            as={Link}
                            href="/"
                            size="sm"
                            className="h-7 bg-[#238638] px-2 text-sm font-semibold text-white hover:bg-[#29903b]">
                            <GoRepo className="mr-1 h-4 w-4" />
                            New
                        </Button>
                    </div>
                    <DashBoardBookList book_list={book_list} />
                </>
            ) : (
                <>
                    <h3 className="mb-4 text-sm font-semibold text-black dark:text-gray-50">Create your first Book</h3>
                    <div className="grid grid-cols-2">
                        <Button
                            as={Link}
                            href="/"
                            size="sm"
                            className="h-8 w-full bg-[#238638] text-sm font-semibold text-white hover:bg-[#29903b]">
                            Create Book
                        </Button>
                    </div>
                </>
            )}
        </div>
    );
}
