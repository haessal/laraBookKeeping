export interface Book {
    id: string;
    name: string;
    is_default: boolean;
    is_owner: boolean;
    modifiable: boolean;
    owner: string;
}
