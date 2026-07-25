import ApplicationLogo from '@/Components/ApplicationLogo';
import NavDropdown from '@/Components/NavDorpdown';
import { NavMenu, NavMenuItem } from '@/Components/NavMenu';
import { PropsWithChildren, ReactNode } from 'react';
import { Footer, FooterCopyright, Navbar, NavbarBrand } from "flowbite-react";

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
                    menu ? "" : "border-b",
                    "border-gray-700 bg-[#0d1117] px-4 py-3 font-sans dark:bg-[#0d1117]"
                ].join(" ")}
            >
                <div className="flex flex-wrap items-center justify-between gap-4 ml-10">
                    <NavbarBrand href="/">
                        <ApplicationLogo className="h-9 w-9 fill-current" />
                    </NavbarBrand>
                    {navTitle}
                </div>
                <NavDropdown />
            </Navbar>
            <NavMenu menu={menu} />

            <main>{children}</main>

            <Footer container className="text-center rounded-none bg-[#0d1117] dark:bg-[#0d1117] py-2">
                <FooterCopyright by="haessal" year={2017} className="text-gray-400" theme={{"span": "ml-1"}}/>
            </Footer>
    </>
  );
}
