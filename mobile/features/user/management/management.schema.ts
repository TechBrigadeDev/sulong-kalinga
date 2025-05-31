import { z } from "zod";
import { beneficiarySchema } from "./schema/beneficiary";
import { careManagerSchema } from "./schema/care-manager";
import { familyMemberSchema } from "./schema/family";
import { careWorkerSchema } from "./schema/care-worker";

export const userManagementSchema = {
  getBeneficiaries: z.object({
    beneficiaries: z.array(beneficiarySchema),
  }),
  getBeneficiary: z.object({
    beneficiary: beneficiarySchema,
  }),
  getFamilyMembers: z.object({
    family_members: z.array(familyMemberSchema),
  }),
  getFamilyMember: z.object({
    family_member: familyMemberSchema,
  }),
  getCareWorkers: z.object({
    careworkers: z.array(careWorkerSchema),
  }),
  getCareWorker: z.object({
    careworker: careWorkerSchema,
  }),
  getCareManagers: z.object({
    caremanagers: z.array(careManagerSchema),
  }),
  getCareManager: z.object({
    caremanager: careManagerSchema,
  }),
};