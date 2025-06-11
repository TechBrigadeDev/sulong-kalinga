import { z } from "zod";

export const isEmail = (
    email: string,
): boolean => {
    const valid = z
        .string()
        .email()
        .safeParse(email);

    return valid.success;
};
