import { Ionicons } from "@expo/vector-icons";
import { useCarePlanForm } from "features/care-plan/form/form";
import { useGetInterventions } from "features/care-plan/hook";
import { useState } from "react";
import { Controller } from "react-hook-form";
import {
    Button,
    Card,
    Checkbox,
    Input,
    ScrollView,
    Text,
    XStack,
    YStack,
} from "tamagui";

export interface SelfSustainabilityIntervention {
    id: string;
    name: string;
    minutes: number;
    isCustom?: boolean;
    interventionId?: number; // Database intervention_id for standard interventions
    categoryId?: number; // care_category_id for custom interventions
    description?: string;
}

// Use the array type directly from schema
export type SelfSustainabilityData =
    SelfSustainabilityIntervention[];

interface SelfSustainabilityProps {
    data?: SelfSustainabilityData;
    onChange?: (
        data: SelfSustainabilityData,
    ) => void;
}

export const SelfSustainability = ({
    data: _data,
    onChange: _onChange,
}: SelfSustainabilityProps) => {
    return (
        <ScrollView>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            fontSize="$6"
                            fontWeight="bold"
                        >
                            Self-Sustainability
                            Interventions
                        </Text>
                        <Text
                            fontSize="$4"
                            color="gray"
                        >
                            Select interventions
                            and specify duration
                            for each
                        </Text>
                    </Card.Header>
                    <YStack p="$4" gap="$4">
                        <InterventionList />
                        <CustomIntervention />
                    </YStack>
                </Card>
            </YStack>
        </ScrollView>
    );
};

const InterventionList = () => {
    const { control } = useCarePlanForm();
    const { interventions } =
        useGetInterventions();

    return (
        <Controller
            control={control}
            name="selfSustainability"
            render={({ field, fieldState }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Available Interventions
                    </Text>

                    {interventions[
                        "Self-sustainability"
                    ].map(
                        (intervention, index) => (
                            <InterventionItem
                                key={index}
                                intervention={
                                    intervention
                                }
                                interventions={
                                    field.value ||
                                    []
                                }
                                onChange={
                                    field.onChange
                                }
                            />
                        ),
                    )}

                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$4"
                            mt="$1"
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

interface InterventionItemProps {
    intervention: {
        intervention_id: number;
        intervention_description: string;
    };
    interventions: SelfSustainabilityIntervention[];
    onChange: (
        interventions: SelfSustainabilityIntervention[],
    ) => void;
}

const InterventionItem = ({
    intervention,
    interventions,
    onChange,
}: InterventionItemProps) => {
    const interventionName =
        intervention.intervention_description;
    const interventionId =
        intervention.intervention_id;

    const existingIntervention =
        interventions.find(
            (i) =>
                i.name === interventionName &&
                !i.isCustom,
        );
    const isChecked = Boolean(
        existingIntervention,
    );

    const toggleIntervention = () => {
        if (isChecked && existingIntervention) {
            // Remove intervention
            onChange(
                interventions.filter(
                    (i) =>
                        i.id !==
                        existingIntervention.id,
                ),
            );
        } else {
            // Add intervention
            const newIntervention: SelfSustainabilityIntervention =
                {
                    id: `self_sustainability_${interventionId}_${Date.now()}`,
                    name: interventionName,
                    minutes: 1,
                    isCustom: false,
                    interventionId:
                        interventionId,
                };
            onChange([
                ...interventions,
                newIntervention,
            ]);
        }
    };

    const updateMinutes = (minutes: string) => {
        if (!existingIntervention) return;

        const minutesNumber =
            parseInt(minutes) || 0;
        onChange(
            interventions.map((i) =>
                i.id === existingIntervention.id
                    ? {
                          ...i,
                          minutes: minutesNumber,
                      }
                    : i,
            ),
        );
    };

    return (
        <XStack gap="$3" items="center">
            <Checkbox
                checked={isChecked}
                onCheckedChange={
                    toggleIntervention
                }
                size="$4"
            >
                <Checkbox.Indicator>
                    <Ionicons
                        name="checkmark"
                        size={16}
                    />
                </Checkbox.Indicator>
            </Checkbox>

            <YStack flex={1} gap="$2">
                <Text fontSize="$4">
                    {interventionName}
                </Text>
                {isChecked && (
                    <XStack
                        gap="$2"
                        items="center"
                    >
                        <Input
                            flex={1}
                            placeholder="Duration"
                            keyboardType="numeric"
                            value={
                                existingIntervention?.minutes?.toString() ||
                                ""
                            }
                            onChangeText={
                                updateMinutes
                            }
                            size="$3"
                        />
                        <Text
                            fontSize="$3"
                            color="gray"
                        >
                            minutes
                        </Text>
                    </XStack>
                )}
            </YStack>
        </XStack>
    );
};

const CustomIntervention = () => {
    const { control } = useCarePlanForm();
    const [customText, setCustomText] =
        useState("");

    return (
        <Controller
            control={control}
            name="selfSustainability"
            render={({ field }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Custom Interventions
                    </Text>

                    <XStack gap="$2">
                        <Input
                            flex={1}
                            placeholder="Enter custom intervention"
                            value={customText}
                            onChangeText={
                                setCustomText
                            }
                        />
                        <Button
                            theme="blue"
                            onPress={() => {
                                if (
                                    customText.trim()
                                ) {
                                    const newIntervention: SelfSustainabilityIntervention =
                                        {
                                            id: Date.now().toString(),
                                            name: customText.trim(),
                                            minutes: 1,
                                            isCustom:
                                                true,
                                        };
                                    field.onChange(
                                        [
                                            ...(field.value ||
                                                []),
                                            newIntervention,
                                        ],
                                    );
                                    setCustomText(
                                        "",
                                    );
                                }
                            }}
                            icon={
                                <Ionicons
                                    name="add"
                                    size={16}
                                />
                            }
                        >
                            Add
                        </Button>
                    </XStack>

                    {(field.value || [])
                        .filter((i) => i.isCustom)
                        .map((intervention) => (
                            <CustomInterventionItem
                                key={
                                    intervention.id
                                }
                                intervention={
                                    intervention
                                }
                                interventions={
                                    field.value ||
                                    []
                                }
                                onChange={
                                    field.onChange
                                }
                            />
                        ))}
                </YStack>
            )}
        />
    );
};

interface CustomInterventionItemProps {
    intervention: SelfSustainabilityIntervention;
    interventions: SelfSustainabilityIntervention[];
    onChange: (
        interventions: SelfSustainabilityIntervention[],
    ) => void;
}

const CustomInterventionItem = ({
    intervention,
    interventions,
    onChange,
}: CustomInterventionItemProps) => {
    const updateMinutes = (minutes: string) => {
        const minutesNumber =
            parseInt(minutes) || 0;
        onChange(
            interventions.map((i) =>
                i.id === intervention.id
                    ? {
                          ...i,
                          minutes: minutesNumber,
                      }
                    : i,
            ),
        );
    };

    const removeIntervention = () => {
        onChange(
            interventions.filter(
                (i) => i.id !== intervention.id,
            ),
        );
    };

    return (
        <XStack
            gap="$3"
            items="center"
            p="$3"
            bg="gray"
            rounded="$4"
        >
            <YStack flex={1} gap="$2">
                <Text fontSize="$4">
                    {intervention.name}
                </Text>
                <XStack gap="$2" items="center">
                    <Input
                        flex={1}
                        placeholder="Duration"
                        keyboardType="numeric"
                        value={
                            intervention.minutes?.toString() ||
                            ""
                        }
                        onChangeText={
                            updateMinutes
                        }
                        size="$3"
                    />
                    <Text
                        fontSize="$3"
                        color="gray"
                    >
                        minutes
                    </Text>
                </XStack>
            </YStack>
            <Button
                theme="red"
                onPress={removeIntervention}
                icon={
                    <Ionicons
                        name="trash-outline"
                        size={16}
                    />
                }
                size="$3"
            />
        </XStack>
    );
};
