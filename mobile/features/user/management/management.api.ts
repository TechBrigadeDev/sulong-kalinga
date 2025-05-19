import { AxiosInstance } from "axios";
import { axiosClient } from "~/common/api";
import { userManagementSchema } from "./management.schema";

class UserManagementController {
  private api: AxiosInstance = axiosClient;

  constructor(token: string) {
    this.api.defaults.headers.common["Accept"] = "application/json";
    this.api.defaults.headers.common["Content-Type"] = "application/json";
    this.api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  }

  async getBeneficiaries(params?: { search?: string }) {
    const response = await this.api.get("/beneficiaries", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });
    
    const data = await response.data;

    const valid = await userManagementSchema.getBeneficiaries.safeParseAsync(data);
    if (!valid.success) {
      console.error("Beneficiaries validation error", valid.error);
      throw new Error("Beneficiaries validation error");
    }

    return valid.data.beneficiaries;
  }

  async getBeneficiary(id: string) {
    const response = await this.api.get(`/beneficiaries/${id}`);
    const data = await response.data;
    
    const valid = await userManagementSchema.getBeneficiary.safeParseAsync(data);
    if (!valid.success) {
      console.error("Beneficiary validation error", valid.error);
      throw new Error("Beneficiary validation error");
    }

    return valid.data.beneficiary;
  }

  async getFamilyMembers(params?: { search?: string }) {
    const response = await this.api.get("/family-members", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });
    const data = await response.data;

    const valid = await userManagementSchema.getFamilyMembers.safeParseAsync(data);
    if (!valid.success) {
      console.error("Family members validation error", valid.error);
      throw new Error("Family members validation error");
    }

    return valid.data.family_members;
  }

  async getFamilyMember(id: string) {
    const response = await this.api.get(`/family-members/${id}`);
    const data = await response.data;

    const valid = await userManagementSchema.getFamilyMember.safeParseAsync(data);
    if (!valid.success) {
      console.error("Family member validation error", valid.error);
      throw new Error("Family member validation error");
    }

    return valid.data.family_member;   
  }
}

export default UserManagementController;
