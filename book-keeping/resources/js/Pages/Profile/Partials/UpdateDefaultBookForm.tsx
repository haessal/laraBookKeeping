import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import type { Book } from '@/types/BookKeeping/v2/Book';
import type { Page } from '@inertiajs/core';
import { useForm } from '@inertiajs/react';
import { Select } from 'flowbite-react';
import { FormEventHandler } from 'react';

type UpdateDefaultBookFormProps = {
    defaultBookId?: string | null;
};

export default function UpdateDefaultBookForm({
    books,
    defaultBookId,
    className = '',
}: {
    books: Book[];
    defaultBookId?: string;
    className?: string;
}) {
    const {
        data,
        setData,
        put,
        delete: destroy,
        processing,
        errors,
    } = useForm({
        book_id: defaultBookId ?? '',
    });

    const isChanged = String(data.book_id) !== String(defaultBookId ?? '');

    const updateDefaultBook: FormEventHandler = (e) => {
        e.preventDefault();

        const options = {
            preserveScroll: true,
            onSuccess: (page: Page) => {
                const latest = (page.props as UpdateDefaultBookFormProps).defaultBookId;
                setData('book_id', String(latest ?? ''));
            },
            onError: () => {
                setData('book_id', defaultBookId ?? '');
            },
        };

        if (data.book_id === '') {
            destroy(route('profile.default-book.destroy'), options);
        } else {
            put(route('profile.default-book.update'), options);
        }
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Default Book</h2>
                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your default book.</p>
            </header>
            <form onSubmit={updateDefaultBook} className="mt-6 space-y-6">
                <Select value={data.book_id} onChange={(e) => setData('book_id', e.target.value)} className="mb-0">
                    <option value="">(No default book)</option>
                    {books.map((book) => (
                        <option key={book.id} value={book.id}>
                            {book.name}
                        </option>
                    ))}
                </Select>
                <InputError message={errors.book_id} className="mt-2 mb-0 text-sm" />
                <PrimaryButton type="submit" disabled={processing || !isChanged} className="mt-5 text-sm">
                    Save
                </PrimaryButton>
            </form>
        </section>
    );
}
