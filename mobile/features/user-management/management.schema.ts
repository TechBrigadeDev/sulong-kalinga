import { z } from "zod";

import { adminSchema } from "./schema/admin";
import { beneficiarySchema } from "./schema/beneficiary";
import { careManagerSchema } from "./schema/care-manager";
import { careWorkerSchema } from "./schema/care-worker";
import { familyMemberSchema } from "./schema/family";

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
    data: familyMemberSchema,
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
  getAdministrators: z.object({
    admins: z.array(
      adminSchema
    ),
  }),
  getAdmin: z.object({
    admin: adminSchema,
  }),
};