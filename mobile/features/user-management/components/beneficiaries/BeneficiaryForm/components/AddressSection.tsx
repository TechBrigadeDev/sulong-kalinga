import { Controller, useFormContext } from "react-hook-form";
import {
    Card,
    H3,
    Input,
    Label,
    Select,
    Text,
    XStack,
    YStack,
} from "tamagui";
import { BeneficiaryFormValues } from "../schema";

// TODO: These should come from an API or store
const MUNICIPALITY_OPTIONS = [
    { label: "Municipality 1", value: "1" },
    { label: "Municipality 2", value: "2" },
];

const BARANGAY_OPTIONS = [
    { label: "Barangay 1", value: "1" },
    { label: "Barangay 2", value: "2" },
];

export const AddressSection = () => {
    const { control } = useFormContext<BeneficiaryFormValues>();

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Current Address</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <Controller
                    control={control}
                    name="street_address"
                    render={({ field, fieldState }) => (
                        <YStack gap="$2">
                            <Label fontWeight="600">
                                House No., Street, Subdivision, Barangay, City, Province *
                            </Label>
                            <Input
                                size="$4"
                                value={field.value || ""}
                                onChangeText={field.onChange}
                                onBlur={field.onBlur}
                                placeholder="Enter complete address"
                            />
                            {fieldState.error && (
                                <Text color="$red10" fontSize="$2">
                                    {fieldState.error.message}
                                </Text>
                            )}
                        </YStack>
                    )}
                />

                <XStack gap="$4">
                    <Controller
                        control={control}
                        name="municipality_id"
                        render={({ field, fieldState }) => (
                            <YStack flex={1} gap="$2">
                                <Label>Municipality *</Label>
                                <Select
                                    value={field.value?.toString() || ""}
                                    onValueChange={(value) => 
                                        field.onChange(value ? parseInt(value) : undefined)
                                    }
                                >
                                    <Select.Trigger>
                                        <Select.Value placeholder="Select municipality" />
                                    </Select.Trigger>
                                    <Select.Content>
                                        {MUNICIPALITY_OPTIONS.map((option, index) => (
                                            <Select.Item
                                                key={option.value}
                                                index={index}
                                                value={option.value}
                                            >
                                                <Select.ItemText>
                                                    {option.label}
                                                </Select.ItemText>
                                            </Select.Item>
                                        ))}
                                    </Select.Content>
                                </Select>
                                {fieldState.error && (
                                    <Text color="$red10" fontSize="$2">
                                        {fieldState.error.message}
                                    </Text>
                                )}
                            </YStack>
                        )}
                    />

                    <Controller
                        control={control}
                        name="barangay_id"
                        render={({ field, fieldState }) => (
                            <YStack flex={1} gap="$2">
                                <Label>Barangay *</Label>
                                <Select
                                    value={field.value?.toString() || ""}
                                    onValueChange={(value) => 
                                        field.onChange(value ? parseInt(value) : undefined)
                                    }
                                >
                                    <Select.Trigger>
                                        <Select.Value placeholder="Select barangay" />
                                    </Select.Trigger>
                                    <Select.Content>
                                        {BARANGAY_OPTIONS.map((option, index) => (
                                            <Select.Item
                                                key={option.value}
                                                index={index}
                                                value={option.value}
                                            >
                                                <Select.ItemText>
                                                    {option.label}
                                                </Select.ItemText>
                                            </Select.Item>
                                        ))}
                                    </Select.Content>
                                </Select>
                                {fieldState.error && (
                                    <Text color="$red10" fontSize="$2">
                                        {fieldState.error.message}
                                    </Text>
                                )}
                            </YStack>
                        )}
                    />
                </XStack>
            </YStack>
        </Card>
    );
};
