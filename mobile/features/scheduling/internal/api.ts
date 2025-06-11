import { Controller } from "common/api";
import { log } from "common/debug";

import {
    internalAppointmentResponseSchema,
    internalAppointmentsResponseSchema,
} from "./schema";

class InternalSchedulingController extends Controller {
    /**
     * Get all internal appointments
     */
    async getSchedules(params?: {
        search?: string;
        status?: string;
        appointment_type_id?: number;
        start_date?: string;
        end_date?: string;
    }) {
        const response = await this.api.get(
            "/internal-appointments",
            {
                params: {
                    ...(params?.search && {
                        search: params.search,
                    }),
                    ...(params?.status && {
                        status: params.status,
                    }),
                    ...(params?.appointment_type_id && {
                        appointment_type_id:
                            params.appointment_type_id,
                    }),
                    ...(params?.start_date && {
                        start_date:
                            params.start_date,
                    }),
                    ...(params?.end_date && {
                        end_date: params.end_date,
                    }),
                },
            },
        );

        log(
            JSON.stringify(
                response.data,
                null,
                2,
            ),
        );

        const valid =
            await internalAppointmentsResponseSchema.safeParseAsync(
                response.data,
            );

        if (!valid.success) {
            console.error(
                "Internal appointments validation error",
                valid.error,
            );
            throw new Error(
                "Internal appointments validation error",
            );
        }

        return valid.data.data;
    }

    /**
     * Get a specific internal appointment by ID
     */
    async getSchedule(id: number | string) {
        const response = await this.api.get(
            `/internal-appointments/${id}`,
        );

        const valid =
            await internalAppointmentResponseSchema.safeParseAsync(
                response.data,
            );

        if (!valid.success) {
            console.error(
                "Internal appointment validation error",
                valid.error,
            );
            throw new Error(
                "Internal appointment validation error",
            );
        }

        return valid.data.data;
    }

    /**
     * Create a new internal appointment
     */
    async createSchedule(appointmentData: {
        appointment_type_id: number;
        title: string;
        description: string;
        other_type_details?: string;
        date: string;
        start_time?: string;
        end_time?: string;
        is_flexible_time: boolean;
        meeting_location: string;
        notes?: string;
        participants: {
            participant_type: string;
            participant_id: number;
            is_organizer: boolean;
        }[];
    }) {
        const response = await this.api.post(
            "/internal-appointments",
            appointmentData,
        );

        const valid =
            await internalAppointmentResponseSchema.safeParseAsync(
                response.data,
            );

        if (!valid.success) {
            console.error(
                "Create internal appointment validation error",
                valid.error,
            );
            throw new Error(
                "Create internal appointment validation error",
            );
        }

        return valid.data.data;
    }

    /**
     * Update an existing internal appointment
     */
    async updateSchedule(
        id: number | string,
        appointmentData: Partial<{
            appointment_type_id: number;
            title: string;
            description: string;
            other_type_details?: string;
            date: string;
            start_time?: string;
            end_time?: string;
            is_flexible_time: boolean;
            meeting_location: string;
            notes?: string;
            status: string;
        }>,
    ) {
        const response = await this.api.put(
            `/internal-appointments/${id}`,
            appointmentData,
        );

        const valid =
            await internalAppointmentResponseSchema.safeParseAsync(
                response.data,
            );

        if (!valid.success) {
            console.error(
                "Update internal appointment validation error",
                valid.error,
            );
            throw new Error(
                "Update internal appointment validation error",
            );
        }

        return valid.data.data;
    }

    /**
     * Delete an internal appointment
     */
    async deleteSchedule(id: number | string) {
        const response = await this.api.delete(
            `/internal-appointments/${id}`,
        );
        return response.data;
    }
}

export const internalSchedulingController =
    new InternalSchedulingController();
