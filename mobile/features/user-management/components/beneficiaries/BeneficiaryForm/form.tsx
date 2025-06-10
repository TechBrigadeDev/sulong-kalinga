import {
    createFormHook,
    createFormHookContexts,
} from "@tanstack/react-form";

export const {
    fieldContext: beneficiaryFieldContext,
    formContext: beneficiaryFormContext,
    useFieldContext: beneficiaryUseFieldContext,
} = createFormHookContexts();

const { useAppForm } = createFormHook({
    fieldContext: beneficiaryFieldContext,
    formContext: beneficiaryFormContext,
    fieldComponents: {},
    formComponents: {},
});

export const useBeneficiaryForm = useAppForm;
