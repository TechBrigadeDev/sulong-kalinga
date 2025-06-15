import { zodResolver } from "@hookform/resolvers/zod";
import { PropsWithChildren } from "react";
import {
    FormProvider,
    useForm,
    useFormContext,
} from "react-hook-form";

import {
    IServiceRequestForm,
    serviceRequestFormSchema,
} from "./schema";

export const ServiceRequestForm = ({
    children,
}: PropsWithChildren) => {
    const form = useForm<IServiceRequestForm>({
        resolver: zodResolver(
            serviceRequestFormSchema,
        ),
        defaultValues: {
            service_type_id: "",
            service_date: "",
            service_time: "",
            message: "",
        },
    });

    return (
        <FormProvider {...form}>
            {children}
        </FormProvider>
    );
};

export const useServiceRequestForm = () =>
    useFormContext<IServiceRequestForm>();
