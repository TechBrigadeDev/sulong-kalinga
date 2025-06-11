import {
    createFormHook,
    createFormHookContexts,
    formOptions,
} from "@tanstack/react-form";

import {
    beneficiaryFormDefaults,
    beneficiaryFormSchema,
} from "./schema";

export const {
    fieldContext: beneficiaryFieldContext,
    formContext: beneficiaryFormContext,
    useFieldContext: useBeneficiaryFieldContext,
    useFormContext: useBeneficiaryFormContext,
} = createFormHookContexts();

export const {
    useAppForm: useBeneficiaryForm,
    withForm: withBeneficiaryForm,
} = createFormHook({
    fieldContext: beneficiaryFieldContext,
    formContext: beneficiaryFormContext,
    fieldComponents: {},
    formComponents: {},
});

export const beneficiaryFormOpts = formOptions({
    defaultValues: beneficiaryFormDefaults,
    validators: {
        onChange: beneficiaryFormSchema,
    },
});
