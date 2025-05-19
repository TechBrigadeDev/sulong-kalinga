import { useQuery } from "@tanstack/react-query"
import { QK } from "../../common/query"
import userController from "./user.api";
import { authStore } from "../auth/auth.store";
import { userStore } from "./user.store";

export const useUser = () => {
    const { token } = authStore()
    const { setUser } = userStore()

    return useQuery({
        queryKey: QK.user.getUser(token as string),
        queryFn: async () => {
            if (!token) {
                throw new Error("Token is required");
            }
            const response = await userController.getUser(token);

            setUser(response);
            return response;
        },
        enabled: !!token,
    })
}