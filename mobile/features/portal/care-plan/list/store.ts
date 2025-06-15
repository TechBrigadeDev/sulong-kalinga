import { create } from "zustand";

interface CarePlanListStore {
    searchTerm: string;
    selectedStatus: string;
    setSearchTerm: (term: string) => void;
    setSelectedStatus: (status: string) => void;
    clearFilters: () => void;
}

export const carePlanListStore = create<CarePlanListStore>((set) => ({
    searchTerm: "",
    selectedStatus: "all",
    setSearchTerm: (term: string) => set({ searchTerm: term }),
    setSelectedStatus: (status: string) => set({ selectedStatus: status }),
    clearFilters: () => set({ searchTerm: "", selectedStatus: "all" }),
}));
