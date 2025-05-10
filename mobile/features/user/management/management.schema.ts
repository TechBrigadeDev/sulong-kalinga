import { z } from "zod";
import { beneficiarySchema } from "../user.schema";


export const userManagementSchema = {
    getBeneficiaries: z.object({
        // beneficiaries: z.array()
    }),
    getBeneficiary: z.object({
        beneficiary: beneficiarySchema
    })
}