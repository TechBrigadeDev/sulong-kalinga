import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import {
    Controller,
    useFormContext,
} from "react-hook-form";
import {
    Card,
    H3,
    Input,
    Label,
    Text,
    XStack,
    YStack,
} from "tamagui";

export const EmergencyContactSection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Emergency Contact</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <XStack gap="$4">
                    <EmergencyContactName />
                    <EmergencyContactRelation />
                </XStack>

                <EmergencyContactMobile />
                <EmergencyProcedure />
            </YStack>
        </Card>
    );
};

const EmergencyContactName = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="emergency_contact_name"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label fontWeight="600">
                        Contact Person Name *
                    </Label>
                    <Input
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter contact name"
                        autoCapitalize="words"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const EmergencyContactRelation = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="emergency_contact_relation"
            render={({ field, fieldState }) => (
                <YStack flex={1} gap="$2">
                    <Label fontWeight="600">
                        Relationship *
                    </Label>
                    <Input
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter relationship"
                        autoCapitalize="words"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const EmergencyContactMobile = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="emergency_contact_mobile"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Mobile Number *
                    </Label>
                    <Input
                        value={
                            field.value?.replace(
                                "+63",
                                "",
                            ) || ""
                        }
                        onChangeText={(value) =>
                            field.onChange(
                                `+63${value}`,
                            )
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter mobile number"
                        keyboardType="phone-pad"
                        maxLength={10}
                        autoComplete="tel"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Format: 9XXXXXXXXX (will
                        be saved as +639XXXXXXXXX)
                    </Text>
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const EmergencyProcedure = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    return (
        <Controller
            control={control}
            name="emergency_procedure"
            render={({ field, fieldState }) => (
                <YStack gap="$2">
                    <Label fontWeight="600">
                        Emergency Procedure
                    </Label>
                    <Input
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        onBlur={field.onBlur}
                        placeholder="Enter emergency procedure"
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                    />
                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$2"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};
