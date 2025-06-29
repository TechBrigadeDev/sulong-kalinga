import { createFormHook } from "@tanstack/react-form";

import DateTimeSection from "./_components/DateTimeSection";
import ServiceDetails from "./_components/ServiceDetails";
import ServiceType from "./_components/ServiceType";
import SubmitService from "./_components/SubmitService";
import {
    serviceFieldContext,
    serviceFormContext,
    serviceFormOpts,
    useServiceFieldContext,
    useServiceFormContext,
} from "./context";

// Re-export the context hooks and options for convenience
export {
    serviceFieldContext,
    serviceFormContext,
    serviceFormOpts,
    useServiceFieldContext,
    useServiceFormContext,
};

export const { useAppForm: useServiceForm } =
    createFormHook({
        fieldContext: serviceFieldContext,
        formContext: serviceFormContext,
        fieldComponents: {
            ServiceTypeField: ServiceType,
            ServiceDetailsField: ServiceDetails,
            DateTimeSectionField: DateTimeSection,
        },
        formComponents: {
            Submit: SubmitService,
        },
    });
