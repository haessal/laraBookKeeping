import { Link, usePage } from '@inertiajs/react';
import { createTheme, Dropdown, DropdownDivider, DropdownItem } from 'flowbite-react';

const customDropdownTheme = createTheme({
    floating: {
        base: 'rounded--lg py-1',
        divider: 'mx-2 my-2 h-px bg-gray-200 dark:bg-gray-700',
        item: {
            container: 'mx-2 my-0 py-0',
            base: 'flex w-full cursor-pointer items-center justify-start px-4 py-1 text-sm hover:rounded-lg text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-200 dark:hover:bg-[#1c2128] dark:hover:text-white dark:focus:bg-[#1c2128] dark:focus:text-white',
        },
        style: {
            dark: 'border bg-gray-700 text-white dark:bg-[#020408]',
            light: 'border border-gray-200 bg-white text-gray-900',
            auto: 'border border-gray-200 bg-white text-gray-900 dark:border-gray-700 dark:bg-[#020408] dark:text-white',
        },
    },
});

export default function NavDropdown() {
    const user = usePage().props.auth.user;

    return (
        <>
            <Dropdown
                label={`login as ${user.name}`}
                theme={customDropdownTheme}
                applyTheme={{
                    floating: {
                        item: {
                            base: 'replace',
                        },
                        style: {
                            dark: 'replace',
                            light: 'replace',
                            auto: 'replace',
                        },
                    },
                }}>
                <DropdownItem as={Link} href={route('dashboard')}>
                    Dashboard
                </DropdownItem>
                <DropdownItem as={Link} href={route('profile.edit')}>
                    Profile
                </DropdownItem>
                <DropdownDivider />
                <DropdownItem as={Link} href={route('logout')} method="post">
                    Log out
                </DropdownItem>
            </Dropdown>
        </>
    );
}
