import { userSchema } from "features/auth/auth.schema";
import { z } from "zod";

// Participant type enum
export const participantTypeSchema = z.enum([
    "cose_user",
]);

/**
 * 

    "appointment_participant_id": 233,
    "appointment_id": 37,
    "full_name": "Benjamin Yap",
    "is_organizer": false,
    "participant_id": 9,
    "participant_type": "cose_user",
    "created_at": "2025-06-20T10:17:43.000000Z",
    "updated_at": "2025-06-20T10:17:43.000000Z"
 */
export const participantSchema = z.object({
    appointment_participant_id: z.number(),
    appointment_id: z.number(),
    full_name: z.string(),
    is_organizer: z.boolean(),
    participant_id: z.number(),
    participant_type: participantTypeSchema,
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
});

export type IParticipantType = z.infer<
    typeof participantTypeSchema
>;
export type IParticipant = z.infer<
    typeof participantSchema
>;
