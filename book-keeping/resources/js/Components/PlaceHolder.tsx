import { Card } from 'flowbite-react';

export default function PlaceHolder({ title }: { title: string }) {
    return (
        <>
            <Card className="m-10 border-gray-700 bg-[#161b22]">
                <h3 className="text-base font-semibold text-gray-200">{title}</h3>
                <p className="text-sm text-gray-500">Coming soon...</p>
            </Card>
        </>
    );
}
