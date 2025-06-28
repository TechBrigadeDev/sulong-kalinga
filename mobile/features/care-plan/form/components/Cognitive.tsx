import { Ionicons } from "@expo/vector-icons";
import { useCarePlanForm } from "features/care-plan/form/form";
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

export interface CognitiveIntervention {
    id: string;
    name: string;
    minutes: number;
    isCustom?: boolean;
    categoryId?: string;
    description?: string;
}

// Use the array type directly from schema
export type CognitiveData =
    CognitiveIntervention[];

interface CognitiveProps {
    data?: CognitiveData;
    onChange?: (data: CognitiveData) => void;
}

const DEFAULT_INTERVENTIONS = [
    "Assist in communication with family members",
    "Help with memory exercises and cognitive games",
    "Support in following daily routines",
    "Aid in using communication devices",
    "Help with reading and writing tasks",
    "Support in decision-making activities",
];

export const Cognitive = ({
    data: _data,
    onChange: _onChange,
}: CognitiveProps) => {
    return (
        <ScrollView>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            fontSize="$6"
                            fontWeight="bold"
                        >
                            Cognitive/Communication
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

    return (
        <Controller
            control={control}
            name="cognitive"
            render={({ field, fieldState }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Available Interventions
                    </Text>

                    {DEFAULT_INTERVENTIONS.map(
                        (
                            interventionName,
                            index,
                        ) => (
                            <InterventionItem
                                key={index}
                                interventionName={
                                    interventionName
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
    interventionName: string;
    interventions: CognitiveIntervention[];
    onChange: (
        interventions: CognitiveIntervention[],
    ) => void;
}

const InterventionItem = ({
    interventionName,
    interventions,
    onChange,
}: InterventionItemProps) => {
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
            const newIntervention: CognitiveIntervention =
                {
                    id: Date.now().toString(),
                    name: interventionName,
                    minutes: 0,
                    isCustom: false,
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
        <XStack gap="$3" ai="center">
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
                    <XStack gap="$2" ai="center">
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
            name="cognitive"
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
                                    const newIntervention: CognitiveIntervention =
                                        {
                                            id: Date.now().toString(),
                                            name: customText.trim(),
                                            minutes: 0,
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
    intervention: CognitiveIntervention;
    interventions: CognitiveIntervention[];
    onChange: (
        interventions: CognitiveIntervention[],
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
            ai="center"
            p="$3"
            bg="gray"
            br="$4"
        >
            <YStack flex={1} gap="$2">
                <Text fontSize="$4">
                    {intervention.name}
                </Text>
                <XStack gap="$2" ai="center">
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
                    <Text fontSize="$3">
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
