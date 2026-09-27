import NavTitle from '@/Components/NavTitle';
import PlaceHolder from '@/Components/PlaceHolder';
import { appName } from '@/config/app';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DashboardSideMenu from '@/Pages/Dashboard/Partials/DashboardSideMenu';
import type { Book } from '@/types/BookKeeping/v2/Book';
import { Head } from '@inertiajs/react';

export default function Dashboard({ book_list }: { book_list: Book[] | null }) {
    return (
        <>
            <Head title={`${appName}`} />
            <AuthenticatedLayout navTitle={<NavTitle title="Dashboard" />}>
                <div className="mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-12 lg:grid-cols-17">
                        <aside className="border-r border-gray-300 bg-gray-50 md:col-span-4 lg:col-span-4 dark:border-gray-700 dark:bg-[#0c1117]">
                            <DashboardSideMenu book_list={book_list} />
                        </aside>

                        <section className="md:col-span-8 lg:col-span-13">
                            <PlaceHolder title="Dashboard" />
                        </section>
                    </div>
                </div>
            </AuthenticatedLayout>
        </>
    );
}
