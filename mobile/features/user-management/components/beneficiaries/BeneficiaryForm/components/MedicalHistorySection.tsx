import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import { Card, H3, YStack } from "tamagui";

import { EnhancedInput } from "./EnhancedInput";

export const MedicalHistorySection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medical History</H3>
            </Card.Header>
            <YStack p="$4" gap="$3">
                <MedicalConditions />
                <Medications />
                <Allergies />
                <Immunizations />
            </YStack>
        </Card>
    );
};

const MedicalConditions = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="medical_conditions"
            render={({ field, fieldState }) => (
                <EnhancedInput
                    label="Medical Conditions"
                    value={field.value || ""}
                    onChangeText={field.onChange}
                    onBlur={field.onBlur}
                    placeholder="List all medical conditions"
                    error={
                        fieldState.error?.message
                    }
                    helperText="Separate multiple conditions with commas"
                    multiline
                    numberOfLines={3}
                    textAlignVertical="top"
                />
            )}
        />
    );
};

const Medications = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="medications"
            render={({ field, fieldState }) => (
                <EnhancedInput
                    label="Medications"
                    value={field.value || ""}
                    onChangeText={field.onChange}
                    onBlur={field.onBlur}
                    placeholder="List all medications"
                    error={
                        fieldState.error?.message
                    }
                    helperText="Separate multiple medications with commas"
                    multiline
                    numberOfLines={3}
                    textAlignVertical="top"
                />
            )}
        />
    );
};

const Allergies = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="allergies"
            render={({ field, fieldState }) => (
                <EnhancedInput
                    label="Allergies"
                    value={field.value || ""}
                    onChangeText={field.onChange}
                    onBlur={field.onBlur}
                    placeholder="List all allergies"
                    error={
                        fieldState.error?.message
                    }
                    helperText="Separate multiple allergies with commas"
                    multiline
                    numberOfLines={3}
                    textAlignVertical="top"
                />
            )}
        />
    );
};

const Immunizations = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="immunizations"
            render={({ field, fieldState }) => (
                <EnhancedInput
                    label="Immunizations"
                    value={field.value || ""}
                    onChangeText={field.onChange}
                    onBlur={field.onBlur}
                    placeholder="List all immunizations"
                    error={
                        fieldState.error?.message
                    }
                    helperText="Separate multiple immunizations with commas"
                    multiline
                    numberOfLines={3}
                    textAlignVertical="top"
                />
            )}
        />
    );
};
