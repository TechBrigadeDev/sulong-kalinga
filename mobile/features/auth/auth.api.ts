import { AxiosError, AxiosInstance } from "axios";
import { axiosClient } from "../../common/api";
import { loginSchema } from "./auth.schema";


class AuthController {
  private jsonApi: AxiosInstance;

  constructor(
    private api: AxiosInstance = axiosClient
  ) {
    // add header Accept: application/json 
    this.jsonApi = api;
    this.jsonApi.defaults.headers.common["Accept"] = "application/json";
    this.jsonApi.defaults.headers.common["Content-Type"] = "application/json";

  }

    async login(email: string, password: string) {
        const formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        try {
            const response = await this.api.post("/login", formData);
            const validate = await loginSchema.response.safeParseAsync(response.data);
            if (!validate.success) {
                throw new Error("Validation failed");
            }

            return {
                success: validate.data.success,
                token: validate.data.token,
            }
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.response?.status) {
                    default:
                        console.error("An error occurred", error.message);
                }
            }
            throw error;
        }
    }

    async logout(token: string) {
        const response = await this.jsonApi.post("/logout", {}, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data;
    }
}

const authController = new AuthController();
export default authController;