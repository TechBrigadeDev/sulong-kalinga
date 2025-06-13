import { zodResolver } from "@hookform/resolvers/zod";
import { PropsWithChildren } from "react";
import {
    FormProvider,
    useForm,
    useFormContext,
} from "react-hook-form";

import {
    beneficiaryFormDefaults,
    beneficiaryFormSchema,
    IBeneficiaryForm,
} from "./schema";

export const BeneficiaryFormProvider = ({
    children,
}: PropsWithChildren) => {
    const methods = useForm<IBeneficiaryForm>({
        resolver: zodResolver(
            beneficiaryFormSchema,
        ),
        defaultValues: beneficiaryFormDefaults,
    });

    return (
        <FormProvider {...methods}>
            {children}
        </FormProvider>
    );
};

export const useBeneficiaryForm = () =>
    useFormContext<IBeneficiaryForm>();

// For compatibility with existing code
export const beneficiaryFormOpts = {
    resolver: zodResolver(beneficiaryFormSchema),
    defaultValues: beneficiaryFormDefaults,
};

// HOC wrapper for components that need form context
export const withBeneficiaryForm = (Component: any) => {
    return (props: any) => (
        <BeneficiaryFormProvider>
            <Component {...props} />
        </BeneficiaryFormProvider>
    );
};
