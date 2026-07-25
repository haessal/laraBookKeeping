export default function NavTitle({
    title,
    bookOwner,
    bookTitle,
}: {
    title?: string;
    bookOwner?: string;
    bookTitle?: string;
}) {
    return (
        <>
            {title && (
                <span className="self-center text-sm font-bold whitespace-nowrap text-white dark:text-white">
                    {title}
                </span>
            )}
            {bookOwner && bookTitle && (
                <span className="flex items-center gap-3 self-center text-sm whitespace-nowrap">
                    <div className="text-white dark:text-white">{bookOwner}</div>
                    <div className="text-gray-500 dark:text-gray-500">/</div>
                    <div className="font-bold text-white dark:text-white">{bookTitle}</div>
                </span>
            )}
        </>
    );
}
