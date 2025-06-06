import * as SecureStore from "expo-secure-store";
import { StateStorage } from "zustand/middleware";

export const secureStorage: StateStorage = {
    getItem: async (key: string): Promise<string | null> => {
        return await SecureStore.getItemAsync(key);
    },
    setItem: async (key: string, value: string): Promise<void> => {
        await SecureStore.setItemAsync(key, value);
    },
    removeItem: async (key: string): Promise<void> => {
        await SecureStore.deleteItemAsync(key);
    },
};
