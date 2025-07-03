import {
    createFormHookContexts,
    formOptions,
} from "@tanstack/react-form";

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
