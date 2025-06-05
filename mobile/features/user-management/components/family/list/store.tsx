import { create } from 'zustand';

interface FamilyListStore {
    search: string;
    setSearch: (search: string) => void;
}

export const familyListStore = create<FamilyListStore>((set) => ({
    search: '',
    setSearch: (search: string) => set({ search }),
}));
