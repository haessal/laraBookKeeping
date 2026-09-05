import ApplicationLogo from '@/Components/ApplicationLogo';
import NavDropdown from '@/Components/NavDorpdown';
import { NavMenu, NavMenuItem } from '@/Components/NavMenu';
import { Footer, FooterCopyright, Navbar, NavbarBrand } from 'flowbite-react';
import { PropsWithChildren, ReactNode } from 'react';

export default function Authenticated({
    navTitle,
    menu,
    children,
}: PropsWithChildren<{ navTitle: ReactNode; menu?: NavMenuItem[] }>) {
    return (
        <>
            <Navbar
                fluid
                rounded={false}
                className={[
                    menu ? '' : 'border-b',
                    'border-gray-700 bg-[#0d1117] px-4 py-3 font-sans dark:bg-[#020408]',
                ].join(' ')}>
                <div className="ml-10 flex flex-wrap items-center justify-between gap-4">
                    <NavbarBrand href={route('dashboard')}>
                        <ApplicationLogo className="h-9 w-9 fill-current" />
                    </NavbarBrand>
                    {navTitle}
                </div>
                <NavDropdown />
            </Navbar>
            <NavMenu menu={menu} />

            <main>{children}</main>

            <Footer container className="rounded-none bg-[#0d1117] py-2 text-center dark:bg-[#0d1117]">
                <FooterCopyright by="haessal" year={2017} className="text-gray-400" theme={{ span: 'ml-1' }} />
            </Footer>
        </>
    );
}
