import { z } from "zod";
import { beneficiarySchema, familyMemberSchema } from "../user.schema";

export const userManagementSchema = {
    getBeneficiaries: z.object({
        beneficiaries: z.array(beneficiarySchema)
    }),
    getBeneficiary: z.object({
        beneficiary: beneficiarySchema
    }),
    getFamilyMembers: z.object({
        family_members: z.array(familyMemberSchema)
    }),
    getFamilyMember: z.object({
        family_member: familyMemberSchema
    }),
}