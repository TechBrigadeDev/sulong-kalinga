import { AxiosInstance } from "axios";
import { axiosClient } from "../../../common/api";
import { userManagementSchema } from "./management.schema";


class UserManagementController {
  private api: AxiosInstance = axiosClient;

  constructor(
      token: string
    ) {
    this.api.defaults.headers.common["Accept"] = "application/json";
    this.api.defaults.headers.common["Content-Type"] = "application/json";
    this.api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  }

  async getBeneficiaries(params?: {
    search?: string;
  }) {
    const response = await this.api.get("/beneficiaries", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });
    
    const data = await response.data;
    return data.beneficiaries;
  }

  async getBeneficiary(id: string) {
    const response = await this.api.get(`/beneficiaries/${id}`);
    const data = await response.data;
    
    console.log("Beneficiary data", data);
    const valid = await userManagementSchema.getBeneficiary.safeParseAsync(data);
    if (!valid.success) {
      console.error("Beneficiary validation error", valid.error);
      throw new Error("Beneficiary validation error");
    }

    return valid.data.beneficiary;
  }
}

export default UserManagementController;
