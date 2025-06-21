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

export interface HouseholdKeepingIntervention {
    id: string;
    name: string;
    minutes: number;
    isCustom?: boolean;
    interventionId?: number; // Database intervention_id for standard interventions
    categoryId?: number; // care_category_id for custom interventions
    description?: string;
}

// Use the array type directly from schema
export type HouseholdKeepingData =
    HouseholdKeepingIntervention[];

interface HouseholdKeepingProps {
    data?: HouseholdKeepingData;
    onChange?: (
        data: HouseholdKeepingData,
    ) => void;
}

export const HouseholdKeeping = ({
    data: _data,
    onChange: _onChange,
}: HouseholdKeepingProps) => {
    return (
        <ScrollView>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            fontSize="$6"
                            fontWeight="bold"
                        >
                            Household Keeping
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
            name="householdKeeping"
            render={({ field, fieldState }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Available Interventions
                    </Text>

                    {interventions[
                        "Household Keeping"
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
    interventions: HouseholdKeepingIntervention[];
    onChange: (
        interventions: HouseholdKeepingIntervention[],
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
            onChange(
                interventions.filter(
                    (i) =>
                        i.id !==
                        existingIntervention.id,
                ),
            );
        } else {
            const newIntervention: HouseholdKeepingIntervention =
                {
                    id: `household_keeping_${interventionId}_${Date.now()}`,
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
    const { getCategoryId } =
        useGetInterventions();
    const [customText, setCustomText] =
        useState("");

    // Get the care_category_id for Household Keeping category
    const householdKeepingCategoryId =
        getCategoryId("Household Keeping");

    return (
        <Controller
            control={control}
            name="householdKeeping"
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
                                    const newIntervention: HouseholdKeepingIntervention =
                                        {
                                            id: `custom_household_keeping_${Date.now()}_${Math.random()}`,
                                            name: customText.trim(),
                                            minutes: 1,
                                            isCustom:
                                                true,
                                            categoryId:
                                                householdKeepingCategoryId,
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
    intervention: HouseholdKeepingIntervention;
    interventions: HouseholdKeepingIntervention[];
    onChange: (
        interventions: HouseholdKeepingIntervention[],
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
