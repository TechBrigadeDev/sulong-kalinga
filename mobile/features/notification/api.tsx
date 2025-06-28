import { AxiosError } from "axios";
import { Controller } from "common/api";
import { log } from "common/debug";
import { paginatedResponseSchema } from "common/schema";
import { IRole } from "features/auth/auth.interface";
import { portalPath } from "features/auth/auth.util";

import { notificationSchema } from "./schema";

class NotificationController extends Controller {
    async getNotifications(params: {
        role: IRole;
        page?: number;
        limit?: number;
        search?: string;
    }) {
        const queryParams = new URLSearchParams();

        if (params?.search) {
            queryParams.append(
                "search",
                params.search,
            );
        }
        if (params?.page) {
            queryParams.append(
                "page",
                params.page.toString(),
            );
        }
        if (params?.limit) {
            queryParams.append(
                "limit",
                params.limit.toString(),
            );
        }

        const path = portalPath(
            params.role,
            "/notifications",
        );
        const url = `${path}${queryParams.toString() ? `?${queryParams.toString()}` : ""}`;

        try {
            const response =
                await this.api.get(url);
            const validate =
                await paginatedResponseSchema(
                    notificationSchema,
                ).safeParseAsync(response.data);

            if (!validate.success) {
                log(
                    "Invalid response data:",
                    validate.error,
                );
                throw new Error(
                    "Invalid response data",
                );
            }

            return validate.data;
        } catch (error) {
            if (error instanceof AxiosError) {
                switch (error.status) {
                    case 500:
                        log(
                            "Server error:",
                            error.message,
                        );
                        break;
                    default:
                        break;
                }
            }

            throw new Error(
                "Failed to fetch notifications",
            );
        }
    }

    async readNotification(
        role: IRole,
        notificationId: string,
    ) {
        const path = portalPath(
            role,
            `/notifications/${notificationId}/read`,
        );

        const response =
            await this.api.post(path);
        return response.data;
    }

    async readAllNotifications(role: IRole) {
        const path = portalPath(
            role,
            "/notifications/read-all",
        );

        const response =
            await this.api.post(path);
        return response.data;
    }
}

const notificationController =
    new NotificationController();
export default notificationController;
