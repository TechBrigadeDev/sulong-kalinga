import { AxiosError } from "axios";
import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import {
    isRoleStaff,
    portalPath,
} from "features/auth/auth.util";

import {
    dtoEmailUpdate,
    dtoUpdatePassword,
} from "./user.interface";
import { userProfileSchema } from "./user.schema";

class UserController extends Controller {
    async getUserProfile(role: IRole) {
        const path = portalPath(
            role,
            isRoleStaff(role)
                ? "/account-profile"
                : "/profile",
        );
        try {
            const response =
                await this.api.get(path);

            log(
                "User profile response:\n",
                "Role:",
                role,
                "\n",
                JSON.stringify(
                    response.data,
                    null,
                    2,
                ),
            );

            const validate =
                await userProfileSchema.safeParseAsync(
                    {
                        ...response.data.data,
                        role,
                    },
                );
            if (!validate.success) {
                log(
                    "User profile validation failed:",
                    response.data.data,
                    validate.error,
                );
                throw new Error(
                    "User profile validation failed",
                );
            }

            return validate.data;
        } catch (error) {
            // console.error(
            //     "Error fetching user profile:",
            //     error,
            // );
            throw error;
        }
    }

    async updateEmail(
        data: dtoEmailUpdate,
        token: string,
    ) {
        try {
            const response = await this.api.patch(
                "/account-profile/email",
                data,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                },
            );

            log(
                "Email update response:",
                response.data,
            );

            return data.new_email;
        } catch (error) {
            console.error(
                "Error updating email:",
                error,
            );
            throw error;
        }
    }

    async updatePassword(
        data: dtoUpdatePassword,
        token: string,
    ) {
        try {
            const response = await this.api.patch(
                "/account-profile/password",
                {
                    current_password:
                        data.current_password,
                    new_password:
                        data.new_password,
                    new_password_confirmation:
                        data.confirm_password,
                },
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                },
            );

            log(
                "Password update response:",
                response.data,
            );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                if (error.status === 422) {
                    console.error(
                        "Validation error:",
                        error.response?.data,
                    );
                }
            }
            throw error;
        }
    }
}

const userController = new UserController();
export default userController;
