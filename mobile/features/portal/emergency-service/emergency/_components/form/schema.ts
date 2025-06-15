import { z } from "zod";

export const emergencyAssistanceFormSchema =
    z.object({
        emergency_type_id: z
            .string()
            .min(1, "Emergency type is required"),
        message: z
            .string()
            .min(
                2,
                "Emergency description is required",
            ),
    });
