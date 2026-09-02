import { HiOutlineBookOpen } from 'react-icons/hi';

export interface Book {
    id: string;
    name: string;
    is_default: boolean;
    is_owner: boolean;
    modifiable: boolean;
    owner: string;
}

export default function DashBoardBookList({ book_list }: { book_list: Book[] }) {
    return (
        <div className="flex flex-col gap-2">
            {book_list.map((book) => (
                <div key={book.id} className="flex items-center gap-2">
                    <HiOutlineBookOpen className="h-4 w-4 shrink-0 text-gray-400" />
                    <div className="min-w-0 flex-1">
                        <div className="flex items-center gap-2">
                            <span className="truncate text-sm font-medium text-black dark:text-[#f0f6fc]">
                                {book.owner} / {book.name}
                            </span>
                            {book.is_default && (
                                <span className="rounded-full border px-2 py-0.5 text-xs text-gray-400 dark:border-gray-600">
                                    Default
                                </span>
                            )}
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
