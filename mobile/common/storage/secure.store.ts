import * as SecureStore from "expo-secure-store";
import { StateStorage } from "zustand/middleware";

export const secureStorage: StateStorage = {
  getItem: async (key: string): Promise<string | null> => {
    const item = await SecureStore.getItemAsync(key);
    console.log("getItem", key, item);
    return item;
  },
  setItem: async (key: string, value: string): Promise<void> => {
    await SecureStore.setItemAsync(key, value);
  },
  removeItem: async (key: string): Promise<void> => {
    await SecureStore.deleteItemAsync(key);
  },
}