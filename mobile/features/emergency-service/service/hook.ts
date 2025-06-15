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
            service_type: "other",
            preferred_date: "",
            preferred_time: "",
            service_details: "",
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
