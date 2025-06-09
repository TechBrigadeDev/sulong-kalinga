import { create } from "zustand";

interface ReportsListState {
    search: string;
    setSearch: (search: string) => void;
}

export const reportsListStore = create<ReportsListState>((set) => ({
    search: "",
    setSearch: (search) => set(() => ({ search })),
}));
