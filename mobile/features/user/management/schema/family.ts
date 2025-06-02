import { z } from "zod";

import { beneficiarySchema } from "./beneficiary";

export const familyMemberSchema = z.object({
    family_member_id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    email: z.string().email(),
    mobile: z.string(),
    relation_to_beneficiary: z.string(),
    is_primary_caregiver: z.boolean(),
    photo: z.string().nullable(),
    photo_url: z.string().nullable(),
    beneficiary: beneficiarySchema,
})