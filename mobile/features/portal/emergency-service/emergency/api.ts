import { AxiosError } from "axios";
import { Controller } from "common/api";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { showToastable } from "react-native-toastable";
import { z } from "zod";

import { IEmergencyForm } from "./_components/form/interface";
import { emergencyTypeSchema } from "./schema";

class EmergencyController extends Controller {
    async getEmergencyTypes(role: IRole) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/emergency/types",
            );

            const response =
                await this.api.get(path);

            const validate = await z
                .object({
                    success: z.boolean(),
                    data: z.array(
                        emergencyTypeSchema,
                    ),
                })
                .parseAsync(response.data);

            if (!validate.success) {
                throw new Error(
                    "Invalid response data",
                );
            }

            return validate.data;
        } catch (error) {
            log(
                "Error fetching emergency types:",
                error,
            );
            throw new Error(
                "Failed to fetch emergency types",
            );
        }
    }

    async postEmergencyRequest(
        data: IEmergencyForm,
        role: IRole,
    ) {
        const path = portalPath(
            role,
            "/emergency-service/emergency/submit",
        );

        try {
            const response = await this.api.post(
                path,
                data,
            );

            log(
                "EmergencyController.postEmergencyRequest",
                JSON.stringify(data, null, 2),
            );

            return response.data;
        } catch (error) {
            showToastable({
                message:
                    "An unexpected error occurred while submitting your emergency request. Please try again later.",
                status: "danger",
                duration: 6000,
            });
            log(
                "Error submitting emergency request:",
                error,
            );
            throw new Error(
                "Failed to submit emergency request",
            );
        }
    }

    async putEmergencyRequest(
        requestId: string,
        data: IEmergencyForm,
        role: IRole,
    ) {
        const path = portalPath(
            role,
            `/emergency-service/emergency/${requestId}`,
        );

        try {
            const response = await this.api.put(
                path,
                data,
            );

            log(
                "EmergencyController.putEmergencyRequest",
                requestId,
            );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 422:
                        console.log(
                            error.response?.data,
                        );
                        showToastable({
                            message:
                                "Invalid data provided. Please check your input and try again.",
                            status: "danger",
                            duration: 6000,
                        });
                        break;
                }
            }
            throw new Error(
                "Failed to update emergency request",
            );
        }
    }

    async deleteEmergencyRequest(
        requestId: string,
        role: IRole,
    ) {
        const path = portalPath(
            role,
            `/emergency-service/emergency/${requestId}`,
        );

        try {
            const response =
                await this.api.delete(path);

            log(
                "EmergencyController.deleteEmergencyRequest",
                requestId,
            );

            return response.data;
        } catch (error) {
            log(
                "Error deleting emergency request:",
                error,
            );
            throw new Error(
                "Failed to delete emergency request",
            );
        }
    }

    async cancelEmergencyRequest(
        requestId: string,
        request: string,
        role: IRole,
    ) {
        const path = portalPath(
            role,
            `/emergency-service/cancel`,
        );

        try {
            const response = await this.api.post(
                path,
                {
                    type: request,
                    id: requestId,
                },
            );

            if (!response.data.success) {
                throw new Error(
                    "Failed to cancel emergency request",
                );
            }

            log(
                "EmergencyController.cancelEmergencyRequest",
                requestId,
            );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                console.log(error.response?.data);
            }
            // log(
            //     "Error canceling emergency request:",
            //     error,
            // );
            throw new Error(
                "Failed to cancel emergency request",
            );
        }
    }
}

const emergencyController =
    new EmergencyController();
export default emergencyController;
