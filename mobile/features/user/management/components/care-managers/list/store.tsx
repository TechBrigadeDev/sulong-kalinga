import { create } from 'zustand';

interface Store {
    search: string;
    setSearch: (search: string) => void;
}

export const careManagerListStore = create<Store>((set) => ({
    search: '',
    setSearch: (search: string) => set({ search }),
}));
