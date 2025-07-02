import { create } from "zustand";
import { secureStorage } from "~/common/storage/secure.store";

interface Shift {
  id: number;
  time_in?: string;
  time_out?: string;
  track_coordinates?: {
    lat: number;
    lng: number;
  };
  [key: string]: any;
}

interface ShiftStore {
  currentShift: Shift | null;
  isOnShift: boolean;
  setCurrentShift: (shift: Shift | null) => void;
  setOnShift: (status: boolean) => void;
  clearShift: () => Promise<void>;
  loadShiftFromStorage: () => Promise<void>; // 🔥
}

export const useShiftStore = create<ShiftStore>((set) => ({
  currentShift: null,
  isOnShift: false,

  setCurrentShift: (shift) => set({ currentShift: shift }),
  setOnShift: (status) => set({ isOnShift: status }),

  clearShift: async () => {
    await secureStorage.removeItem("currentShift");
    await secureStorage.setItem("isOnShift", "false");
    set({ currentShift: null, isOnShift: false });
  },

  loadShiftFromStorage: async () => {
    try {
      const shiftStr = await secureStorage.getItem("currentShift");
      const statusStr = await secureStorage.getItem("isOnShift");

      set({
        currentShift: shiftStr ? JSON.parse(shiftStr) : null,
        isOnShift: statusStr === "true",
      });
    } catch (err) {
      console.error("Failed to load shift from storage", err);
    }
  },
}));
