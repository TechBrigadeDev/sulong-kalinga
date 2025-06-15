import { zodResolver } from "@hookform/resolvers/zod";
import { PropsWithChildren } from "react";
import {
    FormProvider,
    useForm,
    useFormContext,
} from "react-hook-form";

import { IEmergencyForm } from "./interface";
import { emergencyAssistanceFormSchema } from "./schema";

export const EmergencyForm = ({
    children,
}: PropsWithChildren) => {
    const form = useForm<IEmergencyForm>({
        resolver: zodResolver(
            emergencyAssistanceFormSchema,
        ),
        defaultValues: {
            emergency_description: "",
        },
    });
    return (
        <FormProvider {...form}>
            {children}
        </FormProvider>
    );
};

export const useEmergencyForm = () =>
    useFormContext<IEmergencyForm>();
