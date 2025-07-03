import { createFormHook } from "@tanstack/react-form";

import EmergencyDescription from "./_components/Description";
import EmergencyType from "./_components/EmergencyType";
import SubmitEmergency from "./_components/Submit";
import {
    emergencyFieldContext,
    emergencyFormContext,
    emergencyFormOpts,
    useEmergencyFieldContext,
    useEmergencyFormContext,
} from "./context";

// Re-export the context hooks and options for convenience
export {
    emergencyFieldContext,
    emergencyFormContext,
    emergencyFormOpts,
    useEmergencyFieldContext,
    useEmergencyFormContext,
};

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
