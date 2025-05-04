import { AxiosInstance } from "axios";
import { axiosClient } from "../../../common/api";


class UserManagementController {
  private api: AxiosInstance = axiosClient;

  constructor(
      token: string
    ) {
    this.api.defaults.headers.common["Accept"] = "application/json";
    this.api.defaults.headers.common["Content-Type"] = "application/json";
    this.api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  }

  async getBeneficiaries() {
    const response = await this.api.get("/beneficiaries");
    const data = await response.data;
    return data.beneficiaries;
  }
}

export default UserManagementController;
