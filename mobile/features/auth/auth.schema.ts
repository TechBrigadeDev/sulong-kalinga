import { z } from "zod";

export const rolesEnum = z.enum([
    "beneficiary",
    "family_member",
    "care_worker",
    "care_manager",
    "admin",
]);

const baseUserSchema = z.object({
    id: z.number(),
    first_name: z.string(),
    last_name: z.string(),
    mobile: z.string(),
    photo: z.string().nullable(),
    photo_url: z.string().nullable(),
    role: z.string(),
    status: z.string(),
});

const portalBaseUserSchema =
    baseUserSchema.extend({
        family_members: z
            .array(z.any())
            .optional(),
    });

const beneficiaryUserSchema =
    portalBaseUserSchema.extend({
        role: z.literal(
            rolesEnum.enum.beneficiary,
        ),
        username: z.string(),
    });

const familyMemberUserSchema =
    portalBaseUserSchema.extend({
        email: z.string().email(),
        role: z.literal(
            rolesEnum.enum.family_member,
        ),
        related_beneficiary_id: z.number(),
    });

const staffUserSchema = baseUserSchema.extend({
    email: z.string().email(),
    role: z.enum([
        rolesEnum.enum.care_worker,
        rolesEnum.enum.care_manager,
    ]),
});

// Admin/other roles user schema
const adminUserSchema = baseUserSchema.extend({
    email: z.string().email(),
    organization_role_id: z.number(),
    role: z.literal(rolesEnum.enum.admin),
});

// Union schema for user that handles both types
export const userSchema = z.discriminatedUnion(
    "role",
    [
        beneficiaryUserSchema,
        familyMemberUserSchema,
        adminUserSchema,
        staffUserSchema,
    ],
);

export const loginSchema = {
    response: z.object({
        success: z.boolean(),
        token: z.string(),
        user: userSchema,
    }),
};
