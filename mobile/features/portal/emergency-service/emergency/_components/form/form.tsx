import { zodResolver } from "@hookform/resolvers/zod";
import { useEmergencyServiceStore } from "features/portal/emergency-service/store";
import {
    PropsWithChildren,
    useEffect,
} from "react";
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
    const { request } =
        useEmergencyServiceStore().getState();

    const form = useForm<IEmergencyForm>({
        resolver: zodResolver(
            emergencyAssistanceFormSchema,
        ),
        defaultValues: {
            message: request?.description || "",
        },
    });

    useEffect(() => {
        form.reset({
            emergency_type_id: request
                ? request.type
                : "",
            message: request
                ? request.description
                : "",
        });
    }, [request, form]);

    return (
        <FormProvider {...form}>
            {children}
        </FormProvider>
    );
};

export const useEmergencyForm = () =>
    useFormContext<IEmergencyForm>();
