import NavTitle from '@/Components/NavTitle';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PropsWithChildren } from 'react';
import { GoGear, GoHome } from 'react-icons/go';

export default function BookKeepingBookLayout({
    bookId,
    bookOwner,
    bookTitle,
    activeMenu,
    children,
}: PropsWithChildren<{ bookId: string; bookOwner: string; bookTitle: string; activeMenu: string }>) {
    return (
        <AuthenticatedLayout
            navTitle={<NavTitle bookOwner={bookOwner} bookTitle={bookTitle} />}
            menu={[
                {
                    icon: <GoHome className="h-4 w-4" />,
                    label: 'Home',
                    href: route('v2_home', { bookId: bookId }),
                    active: activeMenu === 'home',
                },
                {
                    icon: <GoGear className="h-4 w-4" />,
                    label: 'Settings',
                    href: route('v2_settings', { bookId: bookId }),
                    active: activeMenu === 'settings',
                },
            ]}>
            {children}
        </AuthenticatedLayout>
    );
}
