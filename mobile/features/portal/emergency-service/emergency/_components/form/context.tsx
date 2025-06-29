import {
    createFormHookContexts,
    formOptions,
} from "@tanstack/react-form";

import { emergencyAssistanceFormSchema } from "./schema";

export const emergencyFormOpts = formOptions({
    validators: {
        onChange: emergencyAssistanceFormSchema,
    },
});

export const {
    fieldContext: emergencyFieldContext,
    formContext: emergencyFormContext,
    useFieldContext: useEmergencyFieldContext,
    useFormContext: useEmergencyFormContext,
} = createFormHookContexts();
