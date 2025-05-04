import { useQuery } from "@tanstack/react-query";
import { QK } from "../../../common/query"
import { authStore } from "../../auth/auth.store"
import UserManagementController from "./management.api";


export const useGetBeneficiaries = () => {
    const { token } = authStore();
    if (!token) {
        throw new Error("No token found");
    }

    const api = new UserManagementController(token);

    
    return useQuery({
        queryKey: [QK.user.management.getBeneficiaries, token],
        queryFn: async () => {
            const response = await api.getBeneficiaries();
            console.log("Beneficiaries response", response);
            return response;
        },
        enabled: !!token,
    })
}