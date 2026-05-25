import DangerButton from '@/Components/DangerButton';
import PrimaryButton from '@/Components/PrimaryButton';

import { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';

export default function PersonalAccessTokenForm({ className = '' }: { className?: string }) {
    const { locale } = usePage<PageProps>().props;
    const [token, setToken] = useState<string | null>(null);
    const [createdAt, setCreatedAt] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [copied, setCopied] = useState(false);

    // Fetch token info (created_at only) on initial load
    useEffect(() => {
        axios.get('/profile/token').then((res) => {
            setCreatedAt(res.data.created_at ?? null);
        });
    }, []);

    const generateToken = async () => {
        setLoading(true);
        try {
            const res = await axios.post('/profile/token');
            setToken(res.data.token);
            setCreatedAt(res.data.created_at);
        } finally {
            setLoading(false);
        }
    };

    const deleteToken = async () => {
        setDeleting(true);
        try {
            await axios.delete('/profile/token');
            setToken(null);
            setCreatedAt(null);
        } finally {
            setDeleting(false);
        }
    };

    const copyToClipboard = () => {
        if (!token) return;
        navigator.clipboard.writeText(token);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Personal Access Token</h2>
                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage your token for API access. You can issue up to one token per account. Generating a new token
                    will invalidate your existing token.
                </p>
            </header>

            <div className="mt-6 space-y-6">
                {/* Generate button */}
                <PrimaryButton onClick={generateToken} disabled={loading}>
                    Generate Token
                </PrimaryButton>

                {!createdAt && !token && (
                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">No tokens created.</p>
                )}

                {/* Show token immediately after generation */}
                {token && (
                    <div className="space-y-2">
                        <p className="text-sm font-medium text-amber-600 dark:text-amber-400">
                            This token will not be displayed again. Please copy it now.
                        </p>
                        <div className="flex items-center gap-2">
                            <code className="flex-1 rounded bg-gray-100 px-3 py-2 text-sm break-all dark:bg-gray-700 dark:text-gray-200">
                                {token}
                            </code>
                            <button
                                onClick={copyToClipboard}
                                className="shrink-0 rounded-md bg-gray-200 px-3 py-2 text-sm hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500">
                                {copied ? 'Copied!' : 'Copy'}
                            </button>
                        </div>
                    </div>
                )}

                {createdAt && (
                    <>
                        <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Generated at: {new Date(createdAt!).toLocaleString(locale.replace('_', '-'))}
                        </p>
                        <DangerButton onClick={deleteToken} disabled={deleting}>
                            Delete Token
                        </DangerButton>
                    </>
                )}
            </div>
        </section>
    );
}
