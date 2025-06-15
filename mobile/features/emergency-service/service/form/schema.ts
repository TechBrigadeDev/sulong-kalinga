import { z } from "zod";

export const serviceRequestFormSchema = z.object({
    service_type: z
        .string()
        .min(1, "Service type is required"),
    preferred_date: z
        .string()
        .min(1, "Preferred date is required"),
    preferred_time: z
        .string()
        .min(1, "Preferred time is required"),
    service_details: z
        .string()
        .min(
            10,
            "Please provide more details about your service request (minimum 10 characters)",
        ),
});

export type IServiceRequestForm = z.infer<
    typeof serviceRequestFormSchema
>;
