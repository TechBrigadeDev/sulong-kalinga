import { AxiosInstance, isAxiosError } from "axios";

import { axiosClient } from "~/common/api";

import { userSchema } from "./user.schema";

class UserController {
    private jsonApi: AxiosInstance;

    constructor(private api: AxiosInstance = axiosClient) {
        this.jsonApi = api;
        this.jsonApi.defaults.headers.common["Accept"] = "application/json";
        this.jsonApi.defaults.headers.common["Content-Type"] = "application/json";
    }

    private async health() {
        try {
            const response = await fetch("https://test.cosemhcs.org.ph/health", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
            });
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

    async getUser(token: string) {
        await this.health();

        try {
            const response = await this.jsonApi.get("/user", {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            console.log("User response:", response.data);

            const validate = await userSchema.safeParseAsync(response.data.user);
            if (!validate.success) {
                console.error("User data validation failed:", validate.error);
                throw new Error("User data validation failed");
            }

            return validate.data;
        } catch (error) {
            console.error("Error fetching user data:", error);
            if (isAxiosError(error)) {
                // capture network error
                if (error.response) {
                    console.error("Status:", error.response.status);
                    console.error("Headers:", error.response.headers);
                    console.error("Body:", error.response.data);
                }
                // 2) No response at all—probably a network error or CORS / DNS failure
                else if (error.request) {
                    console.error("No response received. Request was:", error.request);
                }
                // 3) Something went wrong setting up the request
                else {
                    console.error("Axios configuration / setup error:", error.message);
                }
            }
            console.error("Error details:", error);

            throw error;
        }
    }
}

const userController = new UserController();
export default userController;
