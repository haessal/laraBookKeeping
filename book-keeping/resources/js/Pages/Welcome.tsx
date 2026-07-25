import ApplicationLogo from '@/Components/ApplicationLogo';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Button, Footer, FooterCopyright, Navbar, NavbarBrand } from "flowbite-react";


export default function SandboxW({
    auth,
    canLogin,
    canRegister,
}: PageProps<{ canLogin: boolean; canRegister: boolean; laravelVersion: string; phpVersion: string }>) {
    return (
        <div className="flex h-screen flex-col">
            <Head title="Welcome to Bookkeeping" />
            <Navbar
                fluid
                rounded={false}
                className="border-gray-700 bg-[#0d1117] px-4 py-3 font-sans dark:bg-[#0d1117]"
            >
                <div className="flex flex-wrap items-center justify-between gap-4 ml-10">
                    <NavbarBrand href="/">
                        <ApplicationLogo className="h-9 w-9 fill-current" />
                    </NavbarBrand>
                    <span className="self-center whitespace-nowrap text-xl text-white font-bold dark:text-white">Bookkeeping</span>
                </div>
                <div className="flex items-center gap-4 mr-10">
                    {auth.user ? (
                        <Button
                            as={Link}
                            href={route('dashboard')}
                            className="h-10 w-full px-5 text-sm font-semibold text-gray-200 bg-gray-800 hover:bg-gray-700"
                        >
                            Dashboard
                        </Button>
                    ) : (
                        <>
                            {canLogin && (
                                <Button
                                    as={Link}
                                    href={route('login')}
                                    className="h-10 w-full px-5 text-sm font-semibold text-gray-200 bg-gray-800 hover:bg-gray-700"
                                >
                                    Login
                                </Button>
                            )}
                            {canRegister && (
                                <Button
                                    as={Link}
                                    href={route('register')}
                                    className="h-10 w-full px-5 text-sm font-semibold text-gray-200 bg-gray-800 hover:bg-gray-700"
                                >
                                    Register
                                </Button>
                            )}
                        </>
                    )}
                </div>
            </Navbar>

            <main className="flex flex-1 items-center justify-center bg-gray-100 dark:bg-[#0d1117] px-10">
                <h1 className="text-center text-5xl font-sans font-bold leading-tight tracking-tight text-black dark:text-white">
                    Keep your household account book <br/>by double entry
                </h1>
            </main>

            <Footer container className="text-center rounded-none bg-[#0d1117] dark:bg-[#0d1117] py-2">
                <FooterCopyright by="haessal" year={2017} className="text-gray-400" theme={{"span": "ml-1"}}/>
            </Footer>
        </div>
    );
}
