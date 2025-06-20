import { z } from "zod";

export const serviceAssistanceFormSchema =
    z.object({
        service_type_id: z
            .string()
            .min(1, "Service type is required"),
        service_date: z
            .string()
            .min(1, "Preferred date is required"),
        service_time: z
            .string()
            .min(1, "Preferred time is required"),
        message: z
            .string()
            .min(
                10,
                "Please provide more details about your service request (minimum 10 characters)",
            ),
    });
