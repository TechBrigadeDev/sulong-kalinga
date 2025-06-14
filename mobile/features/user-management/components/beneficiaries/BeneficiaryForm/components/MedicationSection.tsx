import { Ionicons } from "@expo/vector-icons";
import { BeneficiaryFormValues } from "features/user-management/components/beneficiaries/BeneficiaryForm/schema";
import { useState } from "react";
import {
    Controller,
    useFieldArray,
    useFormContext,
} from "react-hook-form";
import {
    Button,
    Card,
    H3,
    Label,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { EnhancedInput } from "./EnhancedInput";

interface Medication {
    name: string;
    dosage: string;
    frequency: string;
    instructions?: string;
}

export const MedicationSection = () => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();
    const { fields, append, remove } =
        useFieldArray({
            control,
            name: "medications_list",
        });

    const [
        currentMedication,
        setCurrentMedication,
    ] = useState<Medication>({
        name: "",
        dosage: "",
        frequency: "",
        instructions: "",
    });

    const handleAddMedication = () => {
        if (
            currentMedication.name &&
            currentMedication.dosage
        ) {
            append(currentMedication);
            setCurrentMedication({
                name: "",
                dosage: "",
                frequency: "",
                instructions: "",
            });
        }
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medication Management</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                {fields.map((field, index) => (
                    <MedicationItem
                        key={field.id}
                        index={index}
                        onRemove={() =>
                            remove(index)
                        }
                    />
                ))}

                <Card bordered>
                    <YStack p="$4" gap="$4">
                        <Label fontWeight="600">
                            Add New Medication
                        </Label>

                        <MedicationName
                            value={
                                currentMedication.name
                            }
                            onChange={(value) =>
                                setCurrentMedication(
                                    (prev) => ({
                                        ...prev,
                                        name: value,
                                    }),
                                )
                            }
                        />

                        <MedicationDosage
                            value={
                                currentMedication.dosage
                            }
                            onChange={(value) =>
                                setCurrentMedication(
                                    (prev) => ({
                                        ...prev,
                                        dosage: value,
                                    }),
                                )
                            }
                        />

                        <MedicationFrequency
                            value={
                                currentMedication.frequency
                            }
                            onChange={(value) =>
                                setCurrentMedication(
                                    (prev) => ({
                                        ...prev,
                                        frequency:
                                            value,
                                    }),
                                )
                            }
                        />

                        <MedicationInstructions
                            value={
                                currentMedication.instructions ||
                                ""
                            }
                            onChange={(value) =>
                                setCurrentMedication(
                                    (prev) => ({
                                        ...prev,
                                        instructions:
                                            value,
                                    }),
                                )
                            }
                        />

                        <Button
                            theme="blue"
                            size="$4"
                            onPress={
                                handleAddMedication
                            }
                            disabled={
                                !currentMedication.name ||
                                !currentMedication.dosage
                            }
                            icon={
                                <Ionicons
                                    name="add-outline"
                                    size={20}
                                    color="white"
                                />
                            }
                        >
                            Add Medication
                        </Button>
                    </YStack>
                </Card>
            </YStack>
        </Card>
    );
};

const MedicationItem = ({
    index,
    onRemove,
}: {
    index: number;
    onRemove: () => void;
}) => {
    const { control } =
        useFormContext<BeneficiaryFormValues>();

    return (
        <Card bordered>
            <YStack p="$4" gap="$2">
                <XStack gap="$4">
                    <YStack gap="$2" flex={1}>
                        <Controller
                            control={control}
                            name={`medications_list.${index}.name`}
                            render={({
                                field,
                            }) => (
                                <Text fontWeight="600">
                                    {field.value}
                                </Text>
                            )}
                        />
                        <Controller
                            control={control}
                            name={`medications_list.${index}.dosage`}
                            render={({
                                field,
                            }) => (
                                <Text>
                                    Dosage:{" "}
                                    {field.value}
                                </Text>
                            )}
                        />
                        <Controller
                            control={control}
                            name={`medications_list.${index}.frequency`}
                            render={({
                                field,
                            }) => (
                                <Text>
                                    Frequency:{" "}
                                    {field.value}
                                </Text>
                            )}
                        />
                        <Controller
                            control={control}
                            name={`medications_list.${index}.instructions`}
                            render={({ field }) =>
                                field.value && (
                                    <Text>
                                        Instructions:{" "}
                                        {
                                            field.value
                                        }
                                    </Text>
                                )
                            }
                        />
                    </YStack>
                    <Button
                        theme="red"
                        size="$3"
                        onPress={onRemove}
                    >
                        <Ionicons
                            name="trash-outline"
                            size={20}
                            color="white"
                        />
                    </Button>
                </XStack>
            </YStack>
        </Card>
    );
};

const MedicationName = ({
    value,
    onChange,
}: {
    value: string;
    onChange: (value: string) => void;
}) => (
    <EnhancedInput
        label="Medication Name *"
        placeholder="Enter medication name"
        value={value}
        onChangeText={onChange}
        autoCapitalize="words"
    />
);

const MedicationDosage = ({
    value,
    onChange,
}: {
    value: string;
    onChange: (value: string) => void;
}) => (
    <EnhancedInput
        label="Dosage *"
        placeholder="Enter dosage (e.g., 500mg, 2 tablets)"
        value={value}
        onChangeText={onChange}
        helperText="Include unit (mg, tablets, ml, etc.)"
    />
);

const MedicationFrequency = ({
    value,
    onChange,
}: {
    value: string;
    onChange: (value: string) => void;
}) => (
    <EnhancedInput
        label="Frequency"
        placeholder="Enter frequency (e.g., 2x daily, every 8 hours)"
        value={value}
        onChangeText={onChange}
        helperText="How often to take this medication"
    />
);

const MedicationInstructions = ({
    value,
    onChange,
}: {
    value: string;
    onChange: (value: string) => void;
}) => (
    <EnhancedInput
        label="Instructions"
        placeholder="Enter special instructions"
        value={value}
        onChangeText={onChange}
        multiline
        numberOfLines={2}
        textAlignVertical="top"
        helperText="Any special instructions for taking this medication"
    />
);
