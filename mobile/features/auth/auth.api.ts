import { isEmail } from "common/validate";

import {
    axiosClient,
    Controller,
} from "~/common/api";

import { loginSchema } from "./auth.schema";

class AuthController extends Controller {
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
        console.log(
            "Logging out with token:",
            token,
        );
        const response = await this.api.post(
            "/logout",
            {},
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
        );
        console.log(
            "Logout response:",
            response.data,
        );
        return response.data;
    }
}

const authController = new AuthController();
export default authController;
