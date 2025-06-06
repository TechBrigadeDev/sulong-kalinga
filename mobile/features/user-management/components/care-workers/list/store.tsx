import { create } from "zustand";

interface Store {
    search: string;
    setSearch: (search: string) => void;
}

export const careWorkerListStore = create<Store>((set) => ({
    search: "",
    setSearch: (search: string) => set({ search }),
}));
