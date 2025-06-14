import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import {
    Card,
    H3,
    XStack,
    YStack,
} from "tamagui";

import { EnhancedInput } from "./EnhancedInput";
import { EnhancedSelect } from "./EnhancedSelect";

const MUNICIPALITY_OPTIONS = [
    { label: "Municipality 1", value: "1" },
    { label: "Municipality 2", value: "2" },
    { label: "Makati City", value: "3" },
    { label: "Quezon City", value: "4" },
    { label: "Manila", value: "5" },
];

const BARANGAY_OPTIONS = [
    { label: "Barangay 1", value: "1" },
    { label: "Barangay 2", value: "2" },
    { label: "San Antonio", value: "3" },
    { label: "San Jose", value: "4" },
    { label: "San Miguel", value: "5" },
];

export const AddressSection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Current Address</H3>
            </Card.Header>
            <YStack p="$4" gap="$3">
                <StreetAddress />

                <XStack gap="$3">
                    <Municipality />
                    <Barangay />
                </XStack>
            </YStack>
        </Card>
    );
};

const StreetAddress = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="street_address"
            render={({ field, fieldState }) => (
                <EnhancedInput
                    label="House No., Street, Subdivision, Barangay, City, Province *"
                    value={field.value || ""}
                    onChangeText={field.onChange}
                    onBlur={field.onBlur}
                    placeholder="Enter complete address"
                    error={
                        fieldState.error?.message
                    }
                    multiline
                    numberOfLines={2}
                    textAlignVertical="top"
                    autoCapitalize="words"
                />
            )}
        />
    );
};

const Municipality = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="municipality_id"
            render={({ field, fieldState }) => (
                <YStack flex={1}>
                    <EnhancedSelect
                        label="Municipality *"
                        placeholder="Select municipality"
                        value={
                            field.value?.toString() ||
                            ""
                        }
                        onValueChange={(value) =>
                            field.onChange(
                                value
                                    ? parseInt(
                                          value,
                                      )
                                    : undefined,
                            )
                        }
                        options={
                            MUNICIPALITY_OPTIONS
                        }
                        error={
                            fieldState.error
                                ?.message
                        }
                    />
                </YStack>
            )}
        />
    );
};

const Barangay = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="barangay_id"
            render={({ field, fieldState }) => (
                <YStack flex={1}>
                    <EnhancedSelect
                        label="Barangay *"
                        placeholder="Select barangay"
                        value={
                            field.value?.toString() ||
                            ""
                        }
                        onValueChange={(value) =>
                            field.onChange(
                                value
                                    ? parseInt(
                                          value,
                                      )
                                    : undefined,
                            )
                        }
                        options={BARANGAY_OPTIONS}
                        error={
                            fieldState.error
                                ?.message
                        }
                    />
                </YStack>
            )}
        />
    );
};
