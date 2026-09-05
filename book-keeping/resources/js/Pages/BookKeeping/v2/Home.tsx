import PlaceHolder from '@/Components/PlaceHolder';
import BookKeepingBookLayout from '@/Layouts/BookKeeping/v2/BookLayout';
import type { Book } from '@/types/BookKeeping/v2/Book';
import { Head } from '@inertiajs/react';

export default function Home({ book }: { book: Book }) {
    return (
        <>
            <Head title={`${book.owner}/${book.name}`} />
            <BookKeepingBookLayout bookId={book.id} bookOwner={book.owner} bookTitle={book.name} activeMenu="home">
                <PlaceHolder title="Home" />
            </BookKeepingBookLayout>
        </>
    );
}
