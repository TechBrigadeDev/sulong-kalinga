import {
    createFormHook,
    createFormHookContexts,
    formOptions,
} from "@tanstack/react-form";

import DateTimeSection from "./_components/DateTimeSection";
import ServiceDetails from "./_components/ServiceDetails";
import ServiceType from "./_components/ServiceType";
import SubmitService from "./_components/SubmitService";
import { IServiceForm } from "./interface";
import { serviceAssistanceFormSchema } from "./schema";

export const serviceFormOpts = formOptions({
    defaultValues: {
        service_type_id: "",
        service_date: "",
        service_time: "",
        message: "",
    } as IServiceForm,
    validators: {
        onChange: serviceAssistanceFormSchema,
    },
});

export const {
    fieldContext: serviceFieldContext,
    formContext: serviceFormContext,
    useFieldContext: useServiceFieldContext,
    useFormContext: useServiceFormContext,
} = createFormHookContexts();

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
