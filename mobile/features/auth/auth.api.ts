import { AxiosInstance } from "axios";
import { log } from "common/debug";
import { isEmail } from "common/validate";

import { axiosClient } from "~/common/api";

import { loginSchema } from "./auth.schema";

class AuthController {
    private jsonApi: AxiosInstance;

    constructor(
        private api: AxiosInstance = axiosClient,
    ) {
        this.jsonApi = api;
        this.jsonApi.defaults.headers.common[
            "Accept"
        ] = "application/json";
        this.jsonApi.defaults.headers.common[
            "Content-Type"
        ] = "application/json";
    }

    async login(login: string, password: string) {
        const formData = new FormData();
        if (isEmail(login)) {
            formData.append("email", login);
        } else {
            formData.append("username", login);
        }
        formData.append("password", password);

        const response = await this.api.post(
            "/login",
            formData,
        );
        log(
            "Login successful\n",
            "Role:",
            "\nResponse data:",
            JSON.stringify(
                response.data,
                null,
                2,
            ),
        );
        const validate =
            await loginSchema.response.safeParseAsync(
                response.data,
            );
        if (!validate.success) {
            console.error(
                "Login response validation failed",
                validate.error.errors,
            );
            throw new Error("Validation failed");
        }

        axiosClient.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${validate.data.token}`;

        return {
            success: validate.data.success,
            token: validate.data.token,
            user: validate.data.user,
        };
    }

    async logout(token: string) {
        const response = await this.jsonApi.post(
            "/logout",
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
        );
        return response.data;
    }
}

const authController = new AuthController();
export default authController;
