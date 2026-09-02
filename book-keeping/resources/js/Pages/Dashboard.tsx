import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Book } from '@/Components/Dashboard/DashboardBookList';
import DashboardSideMenu from '@/Components/Dashboard/DashboardSideMenu';
import NavTitle from '@/Components/NavTitle';
import { Head } from '@inertiajs/react';
import { Card } from 'flowbite-react';

export default function Dashboard({ book_list }: {book_list: Book[] | null}) {
    return (
        <>
            <Head title="Dashboard" />
            <AuthenticatedLayout navTitle={<NavTitle title="Dashboard" />}>
                <div className="mx-auto">
                    <div className="grid grid-cols-1 md:grid-cols-12 lg:grid-cols-17">
                        <aside className="border-r border-gray-300 bg-gray-50 md:col-span-4 lg:col-span-4 dark:border-gray-700 dark:bg-[#0c1117]">
                            <DashboardSideMenu book_list={book_list}/>
                        </aside>

                        <section className="md:col-span-8 lg:col-span-13">
                            <Card className="m-10 border-gray-700 bg-[#161b22]">
                                <h3 className="text-base font-semibold text-gray-200">News</h3>
                                <p className="text-sm text-gray-500">Coming soon...</p>
                            </Card>
                        </section>
                    </div>
                </div>
            </AuthenticatedLayout>
        </>
    );
}
