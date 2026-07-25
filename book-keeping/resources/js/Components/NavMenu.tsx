import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

export type NavMenuItem = {
    icon?: ReactNode;
    label: string;
    href: string;
    active?: boolean;
};

export function NavMenu({ menu }: { menu?: NavMenuItem[] }) {
    if (!menu || menu.length === 0) {
        return null;
    }

    return (
        <>
            <nav className="border-b border-gray-700 bg-[#0d1117] px-4 dark:bg-[#0d1117]">
                <ul className="ml-10 flex flex-wrap items-center gap-1 text-sm">
                    {menu.map((item) => {
                        return (
                            <li key={item.href}>
                                <Link
                                    href={item.href}
                                    className={[
                                        'flex items-center gap-2 border-b-2 px-3 py-2.5 font-medium transition-colors',
                                        item.active
                                            ? 'border-orange-500 text-white'
                                            : 'border-transparent text-gray-400 hover:border-gray-500 hover:text-white',
                                    ].join(' ')}>
                                    {item.icon}
                                    {item.label}
                                </Link>
                            </li>
                        );
                    })}
                </ul>
            </nav>
        </>
    );
}
