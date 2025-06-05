import { AxiosError, AxiosInstance } from "axios";

import { axiosClient } from "~/common/api";

import { loginSchema } from "./auth.schema";


class AuthController {
  private jsonApi: AxiosInstance;

  constructor(
    private api: AxiosInstance = axiosClient
  ) {
    this.jsonApi = api;
    this.jsonApi.defaults.headers.common["Accept"] = "application/json";
    this.jsonApi.defaults.headers.common["Content-Type"] = "application/json";
  }
        
        
    private async health(){
        console.info("test:", this.jsonApi.defaults.baseURL)
      try {
        const response = await fetch("https://test.cosemhcs.org.ph/health", {
          method: "GET",
        });
        console.info({response})
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Health check response:", data);
      } catch (error) {
        console.error("Error during health check:", error);
        throw error;
      }
    }


    async login(email: string, password: string) {
        await this.health();
        const formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        try {
            const response = await this.api.post("/login", formData);
            console.log("Login response:", response);
            const validate = await loginSchema.response.safeParseAsync(response.data);
            if (!validate.success) {
                throw new Error("Validation failed");
            }

            axiosClient.defaults.headers.common["Authorization"] = `Bearer ${validate.data.token}`;

            return {
                success: validate.data.success,
                token: validate.data.token,
            }
        } catch (error) {
            console.error("Error during login:", error);
            if (error instanceof AxiosError) {
                console.error("Axios error occurred:", error.toJSON());
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