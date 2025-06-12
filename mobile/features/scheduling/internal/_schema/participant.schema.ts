import { userSchema } from "features/user/user.schema";
import { z } from "zod";

// Participant type enum
export const participantTypeSchema = z.enum([
    "cose_user",
]);

export const participantSchema = z.object({
    appointment_participant_id: z.number(),
    appointment_id: z.number(),
    participant_type: participantTypeSchema,
    participant_id: z.number(),
    is_organizer: z.boolean(),
    created_at: z.string().datetime(),
    updated_at: z.string().datetime(),
    user: userSchema.optional(),
});

export type IParticipantType = z.infer<
    typeof participantTypeSchema
>;
export type IParticipant = z.infer<
    typeof participantSchema
>;
