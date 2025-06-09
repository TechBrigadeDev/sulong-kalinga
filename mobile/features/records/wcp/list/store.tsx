import { create } from "zustand";

interface WCPRecordsListState {
    search: string;
    setSearch: (search: string) => void;
}

export const wcpRecordsListStore =
    create<WCPRecordsListState>((set) => ({
        search: "",
        setSearch: (search) =>
            set(() => ({ search })),
    }));
