import { AxiosInstance } from "axios";
import { axiosClient } from "~/common/api";
import { userManagementSchema } from "./management.schema";
import { PaginatedResponse, IBeneficiary } from "./management.type";

class UserManagementController {
  private api: AxiosInstance = axiosClient;

  constructor(token: string) {
    this.api.defaults.headers.common["Accept"] = "application/json";
    this.api.defaults.headers.common["Content-Type"] = "application/json";
    this.api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  }

  async getBeneficiaries(params?: { 
    search?: string;
    page?: number;
    limit?: number;
  }): Promise<PaginatedResponse<IBeneficiary>> {
    const response = await this.api.get("/beneficiaries", {
      params: {
        ...(params?.search && { search: params.search }),
        ...(params?.page && { page: params.page }),
        ...(params?.limit && { limit: params.limit }),
      }
    });
    
    const data = await response.data;

    const valid = await userManagementSchema.getBeneficiaries.safeParseAsync(data);
    if (!valid.success) {
      console.error("Beneficiaries validation error", valid.error);
      throw new Error("Beneficiaries validation error");
    }

    return {
      data: valid.data.beneficiaries,
      meta: valid.data.meta
    };
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
    try {
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
    } catch (error) {
      console.error("\n\n\nError fetching family members:", error);
      throw error;
    }
  }

  async getFamilyMember(id: string) {
    const response = await this.api.get(`/family-members/${id}`);
    if (!response.data) {
      throw new Error("No data received from API");
    }
    const data = await response.data;
    console.log(JSON.stringify(data, null, 2));

    const valid = await userManagementSchema.getFamilyMember.safeParseAsync(data);
    if (!valid.success) {
      console.error("Family member validation error", valid.error);
      throw new Error("Family member validation error");
    }

    return valid.data.data;   
  }

  async getCareWorkers(params?: { search?: string }) {
    const response = await this.api.get("/care-workers", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });
    
    const data = await response.data;
    const valid = await userManagementSchema.getCareWorkers.safeParseAsync(data);
    if (!valid.success) {
      console.error("Care workers validation error", valid.error);
      throw new Error("Care workers validation error");
    }

    return valid.data.careworkers;
  }

  async getCareWorker(id: string) {
    const response = await this.api.get(`/care-workers/${id}`);
    if (!response.data) {
      throw new Error("No data received from API");
    }
    const data = await response.data;

    const valid = await userManagementSchema.getCareWorker.safeParseAsync(data);
    if (!valid.success) {
      console.error("Care worker validation error", valid.error);
      throw new Error("Care worker validation error");
    }

    return valid.data.careworker;
  }

  async getCareManagers(params?: { search?: string }) {
    const response = await this.api.get("/care-managers", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });
    const data = await response.data;
    
    const valid = await userManagementSchema.getCareManagers.safeParseAsync(data);
    if (!valid.success) {
      console.error("Care managers validation error", valid.error);
      throw new Error("Care managers validation error");
    }

    return valid.data.caremanagers;
  }

  async getCareManager(id: string) {
    const response = await this.api.get(`/care-managers/${id}`);
    if (!response.data) {
      throw new Error("No data received from API");
    }
    const data = await response.data;

    const valid = await userManagementSchema.getCareManager.safeParseAsync(data);
    if (!valid.success) {
      console.error("Care manager validation error", valid.error);
      throw new Error("Care manager validation error");
    }

    return valid.data.caremanager;
  }

  async getAdministrators(params?: { search?: string }) {
    const response = await this.api.get("/admins", {
      params: {
        ...(params?.search && { search: params.search }),
      }
    });

    const data = await response.data;

    const valid = await userManagementSchema.getAdministrators.safeParseAsync(data);
    if (!valid.success) {
      console.error("Administrators validation error", valid.error);
      throw new Error("Administrators validation error");
    }

    return valid.data.admins;
  }

  async getAdmin(id: string) {
    const response = await this.api.get(`/admins/${id}`);
    if (!response.data) {
      throw new Error("No data received from API");
    }
    const data = await response.data;

    const valid = await userManagementSchema.getAdmin.safeParseAsync(data);
    if (!valid.success) {
      console.error("Admin validation error", valid.error);
      throw new Error("Admin validation error");
    }

    return valid.data.admin;
  }
}

export default UserManagementController;
