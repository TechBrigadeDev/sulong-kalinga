import { type z } from "zod";

import { beneficiarySchema } from "./schema/beneficiary";
import { careManagerSchema } from "./schema/care-manager";
import { careWorkerSchema } from "./schema/care-worker";
import { familyMemberSchema } from "./schema/family";

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export type IBeneficiary = z.infer<
    typeof beneficiarySchema
>;

export type ICareManager = z.infer<
    typeof careManagerSchema
>;

export type ICareWorker = z.infer<
    typeof careWorkerSchema
>;

export type IFamilyMember = z.infer<
    typeof familyMemberSchema
>;
