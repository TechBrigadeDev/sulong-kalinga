import {
    AxiosError,
    AxiosInstance,
    isAxiosError,
} from "axios";
import { log } from "common/debug";

import { axiosClient } from "~/common/api";

import {
    dtoEmailUpdate,
    dtoUpdatePassword,
} from "./user.interface";
import {
    userProfileSchema,
    userSchema,
} from "./user.schema";

class UserController {
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

    async getUser(token: string) {
        try {
            const response =
                await this.jsonApi.get("/user", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
            console.log(
                "User response:",
                response.data,
            );

            const validate =
                await userSchema.safeParseAsync(
                    response.data.user,
                );
            if (!validate.success) {
                console.error(
                    "User data validation failed:",
                    validate.error,
                );
                throw new Error(
                    "User data validation failed",
                );
            }

            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching user data:",
                error,
            );
            if (isAxiosError(error)) {
                // capture network error
                if (error.response) {
                    console.error(
                        "Status:",
                        error.response.status,
                    );
                    console.error(
                        "Headers:",
                        error.response.headers,
                    );
                    console.error(
                        "Body:",
                        error.response.data,
                    );
                }
                // 2) No response at all—probably a network error or CORS / DNS failure
                else if (error.request) {
                    console.error(
                        "No response received. Request was:",
                        error.request,
                    );
                }
                // 3) Something went wrong setting up the request
                else {
                    console.error(
                        "Axios configuration / setup error:",
                        error.message,
                    );
                }
            }
            console.error(
                "Error details:",
                error,
            );

            throw error;
        }
    }

    async getUserProfile(token: string) {
        try {
            const response =
                await this.jsonApi.get(
                    "/account-profile",
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    },
                );

            const validate =
                await userProfileSchema.safeParseAsync(
                    response.data.data,
                );
            if (!validate.success) {
                console.error(
                    "User profile validation failed:",
                    validate.error,
                );
                throw new Error(
                    "User profile validation failed",
                );
            }

            return validate.data;
        } catch (error) {
            console.error(
                "Error fetching user profile:",
                error,
            );
            throw error;
        }
    }

    async updateEmail(
        data: dtoEmailUpdate,
        token: string,
    ) {
        try {
            const response =
                await this.jsonApi.patch(
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
            const response =
                await this.jsonApi.patch(
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
