import {
    createFormHook,
    createFormHookContexts,
    formOptions,
} from "@tanstack/react-form";

import EmergencyDescription from "./_components/Description";
import EmergencyType from "./_components/EmergencyType";
import SubmitEmergency from "./_components/Submit";
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

export const { useAppForm: useEmergencyForm } =
    createFormHook({
        fieldContext: emergencyFieldContext,
        formContext: emergencyFormContext,
        fieldComponents: {
            TypeField: EmergencyType,
            DescriptionField:
                EmergencyDescription,
        },
        formComponents: {
            Submit: SubmitEmergency,
        },
    });
