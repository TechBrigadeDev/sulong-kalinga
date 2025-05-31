import { z } from "zod";
import { careWorkerSchema, familyMemberSchema } from "../user.schema";
import { beneficiarySchema } from "./schema/beneficiary";

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
    getCareWorkers: z.object({
        careworkers: z.array(careWorkerSchema)
    }),
    getCareWorker: z.object({
        careworker: careWorkerSchema
    }),
    getCareManagers: z.object({
        care_managers: z.array(careWorkerSchema)
    }),
}