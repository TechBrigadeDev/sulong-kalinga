import { zodResolver } from "@hookform/resolvers/zod";
import { isDev } from "common/env";
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
                illness: isDev
                    ? "Stable condition with no acute issues."
                    : "",
                assessment: isDev
                    ? "Patient is stable with no acute issues."
                    : "",
                bloodPressure: isDev
                    ? "120/80"
                    : "",
                pulseRate: isDev ? 72 : undefined,
                temperature: isDev
                    ? 36.6
                    : undefined,
                respiratoryRate: isDev
                    ? 16
                    : undefined,
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
                recommendations: isDev
                    ? "Continue current care plan with regular follow-ups."
                    : "",
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
