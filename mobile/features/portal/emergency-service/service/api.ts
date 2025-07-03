import { AxiosError } from "axios";
import { Controller } from "common/api";
import { formatTime } from "common/date";
import { log } from "common/debug";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";
import { showToastable } from "react-native-toastable";
import { z } from "zod";

import { IServiceForm } from "./_components/form/interface";
import { serviceTypes } from "./schema";

class ServiceController extends Controller {
    async getServiceTypes(role: IRole) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/service/types",
            );

            const response =
                await this.api.get(path);

            const validate = await z
                .object({
                    success: z.boolean(),
                    data: z.array(serviceTypes),
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

    async postServiceRequest(
        role: IRole,
        data: IServiceForm,
    ) {
        try {
            const path = portalPath(
                role,
                "/emergency-service/service/submit",
            );

            const response = await this.api.post(
                path,
                data,
            );

            return response.data;
        } catch (error) {
            log(
                "Error submitting service request:",
                error,
            );
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 422:
                        console.log(
                            "Invalid data provided:",
                            error.response?.data,
                            data,
                        );
                        showToastable({
                            message:
                                "Invalid data provided. Please check your input and try again.",
                            status: "danger",
                            duration: 6000,
                        });
                }
            }
            throw new Error(
                "Failed to submit service request",
            );
        }
    }

    async putServiceRequest(
        role: IRole,
        id: string,
        data: IServiceForm,
    ) {
        try {
            const path = portalPath(
                role,
                `/emergency-service/service/${id}`,
            );

            const response = await this.api.put(
                path,
                {
                    ...data,
                    service_time: formatTime(
                        data.service_time,
                        "HH:mm",
                    ),
                } as IServiceForm,
            );

            return response.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 422:
                        console.log(
                            "Invalid data provided:",
                            error.response?.data,
                            data,
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
                "Failed to update service request",
            );
        }
    }
}

const serviceController = new ServiceController();
export default serviceController;
