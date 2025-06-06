import { AxiosInstance } from "axios";
import { axiosClient } from "common/api";

class AppController {
    private api: AxiosInstance = axiosClient;

    public async health() {
        console.info("test:", this.api.defaults.baseURL);
        try {
            const response = await fetch("https://test.cosemhcs.org.ph/health", {
                method: "GET",
            });
            console.info({ response });
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
}

export const appController = new AppController();
