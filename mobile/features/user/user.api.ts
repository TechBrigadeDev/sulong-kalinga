import { AxiosInstance } from "axios";
import { axiosClient } from "~/common/api";
import { userSchema } from "./user.schema";

class UserController {
    private jsonApi: AxiosInstance;
  
    constructor(
      private api: AxiosInstance = axiosClient
    ) {
      this.jsonApi = api;
      this.jsonApi.defaults.headers.common["Accept"] = "application/json";
      this.jsonApi.defaults.headers.common["Content-Type"] = "application/json";
    }

    async getUser(token: string) {
        const response = await this.jsonApi.get("/user", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        const validate = await userSchema.safeParseAsync(response.data.user);
        if (!validate.success) {
            console.error("User data validation failed:", validate.error);
            throw new Error("User data validation failed");
        }

        return validate.data;
    }
}  

const userController = new UserController();
export default userController;