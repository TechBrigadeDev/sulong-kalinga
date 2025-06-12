import { zodResolver } from "@hookform/resolvers/zod";
import { PropsWithChildren } from "react";
import {
    FormProvider,
    useForm,
    useFormContext,
} from "react-hook-form";

import { carePlanFormSchema } from "./schema";
import { CarePlanFormData } from "./type";

export const CarePlanForm = ({
    children,
}: PropsWithChildren) => {
    const form = useForm<CarePlanFormData>({
        resolver: zodResolver(carePlanFormSchema),
        defaultValues: {
            personalDetails: {
                beneficiaryId: "",
                illness: "",
                assessment: "",
                bloodPressure: "",
                pulseRate: undefined,
                temperature: undefined,
                respiratoryRate: undefined,
            },
            mobility: [],
            cognitive: [],
            selfSustainability: [],
            diseaseTherapy: [],
            socialContact: [],
            outdoorActivity: [],
            householdKeeping: [],
            evaluation: {
                pictureUri: "",
                recommendations: "",
            },
        },
    });
    return (
        <FormProvider {...form}>
            {children}
        </FormProvider>
    );
};

export const useCarePlanForm = () =>
    useFormContext<CarePlanFormData>();
