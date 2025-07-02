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
        role: IRole,
    ) {
        const isStaff = isRoleStaff(role);

        const formData = {
            email: data.new_email,
            new_email: data.new_email,
            password: data.current_password,
        };

        try {
            const response = isStaff
                ? await this.api.patch(
                      "/account-profile/email",
                      formData,
                  )
                : await this.api.post(
                      portalPath(
                          role,
                          "/profile/update-email",
                      ),
                      formData,
                  );

            log(
                "Email update response:",
                response.data,
            );

            return data.new_email;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 422:
                        console.error(
                            "Validation error:",
                            error.response?.data,
                        );
                        break;
                }
            }
            console.error(
                "Error updating email:",
                error,
                JSON.stringify(error, null, 2),
            );
            throw error;
        }
    }

    async updatePassword(
        data: dtoUpdatePassword,
        role: IRole,
    ) {
        const isStaff = isRoleStaff(role);

        const formData = {
            current_password:
                data.current_password,
            new_password: data.new_password,
            new_password_confirmation:
                data.confirm_password,
        };

        try {
            const response = isStaff
                ? await this.api.patch(
                      "/account-profile/password",
                      formData,
                  )
                : await this.api.post(
                      portalPath(
                          role,
                          "/profile/update-password",
                      ),
                      formData,
                  );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 422:
                        console.error(
                            "Validation error:",
                            error.response?.data,
                        );
                        break;
                }
            }
            console.error(
                "Error updating password:",
                error,
            );
            throw error;
        }
    }
}

const userController = new UserController();
export default userController;
