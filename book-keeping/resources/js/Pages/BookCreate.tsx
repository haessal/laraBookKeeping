import NavTitle from '@/Components/NavTitle';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Form, Head } from '@inertiajs/react';
import { Button, Label, TextInput } from 'flowbite-react';

export default function BookCreate() {
    return (
        <>
            <Head title="New book" />
            <AuthenticatedLayout navTitle={<NavTitle title="New book" />}>
                <div className="py-1.5">
                    <div className="mx-auto max-w-7xl px-8">
                        <h2 className="py-3 pl-2 text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">
                            Create a new book
                        </h2>
                        <div className="rounded-lg border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                            <div className="bg-white p-4 text-black dark:bg-gray-950 dark:text-gray-200">
                                <Form action={route('books.store')} method="post" className="flex flex-col gap-4">
                                    <div>
                                        <div className="mb-2 block">
                                            <Label htmlFor="bool_title">Book Title</Label>
                                        </div>
                                        <TextInput id="bool_title" name="name" type="text" required />
                                    </div>
                                    <div className="flex items-center justify-end gap-2">
                                        <Button
                                            className="bg-[#238638] px-2 text-sm font-semibold text-white hover:bg-[#29903b]"
                                            type="submit">
                                            Create Book
                                        </Button>
                                    </div>
                                </Form>
                            </div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        </>
    );
}
