import { Head } from '@inertiajs/react';

interface Props {
    laravelVersion: string;
    phpVersion: string;
}

export default function FlowbiteSample({ laravelVersion, phpVersion }: Props) {
    return (
        <>
            <Head title="Flowbite Sample" />

            <div className="min-h-screen bg-gray-50 p-8 dark:bg-gray-900">
                <div className="mx-auto max-w-4xl">
                    {/* ヘッダー */}
                    <h1 className="mb-8 text-3xl font-bold text-gray-900 dark:text-white">Flowbite Sample</h1>

                    {/* バージョン情報カード */}
                    <div className="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">環境情報</h2>
                        <ul className="space-y-2 text-gray-600 dark:text-gray-400">
                            <li>Laravel: {laravelVersion}</li>
                            <li>PHP: {phpVersion}</li>
                        </ul>
                    </div>

                    {/* ボタンのサンプル */}
                    <div className="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">ボタン</h2>
                        <div className="flex flex-wrap gap-4">
                            <button
                                type="button"
                                className="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Default
                            </button>
                            <button
                                type="button"
                                className="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 focus:ring-4 focus:ring-green-300 focus:outline-none dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                Success
                            </button>
                            <button
                                type="button"
                                className="rounded-lg bg-red-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800 focus:ring-4 focus:ring-red-300 focus:outline-none dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                Danger
                            </button>
                        </div>
                    </div>

                    {/* アラートのサンプル */}
                    <div className="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">アラート</h2>
                        <div className="space-y-4">
                            <div
                                className="rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-gray-800 dark:text-blue-400"
                                role="alert">
                                情報メッセージです。
                            </div>
                            <div
                                className="rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-gray-800 dark:text-red-400"
                                role="alert">
                                エラーメッセージです。
                            </div>
                            <div
                                className="rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-gray-800 dark:text-green-400"
                                role="alert">
                                成功メッセージです。
                            </div>
                        </div>
                    </div>

                    {/* バッジのサンプル */}
                    <div className="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">バッジ</h2>
                        <div className="flex flex-wrap gap-2">
                            <span className="rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Default
                            </span>
                            <span className="rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                Success
                            </span>
                            <span className="rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                                Danger
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
