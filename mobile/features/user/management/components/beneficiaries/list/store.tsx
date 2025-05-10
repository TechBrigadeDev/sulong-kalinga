import { create } from "zustand";

interface BeneficiaryListState {
    search: string;
    setSearch: (search: string) => void;
}

export const beneficiaryListStore = create<BeneficiaryListState>((set) => ({
    search: "",
    setSearch: (search) => set(() => ({ search }))
}));

