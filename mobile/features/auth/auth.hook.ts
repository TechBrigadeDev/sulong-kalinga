import { useRouter } from "expo-router";
import { authStore } from "./auth.store"


export const useLogin = () => {
    const { 
        setToken
    } = authStore();
    const router = useRouter();

    const login = async (email: string, password: string) => {
        
        setToken(JSON.stringify({
            email,
            password
        }));
        router.replace("/(tabs)");
    };


    return {
        login,
    }
}


export const useLogout = () => {
    const { 
        setToken
    } = authStore();
    const router = useRouter();

    const logout = async () => {
        setToken(null);
        router.replace("/login");
    };

    return {
        logout,
    }
}