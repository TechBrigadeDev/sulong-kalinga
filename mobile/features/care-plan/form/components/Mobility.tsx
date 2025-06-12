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

export interface MobilityIntervention {
    id: string;
    name: string;
    minutes: number;
    isCustom?: boolean;
    categoryId?: string;
    description?: string;
}

// Use the array type directly from schema
export type MobilityData = MobilityIntervention[];

const DEFAULT_INTERVENTIONS = [
    "Assist/aid in sitting",
    "Support/aid in walking and other movements",
    "Transfer/move from bed to wheelchair",
    "Aide in using assistive device",
    "Assist in using the toilet",
    "Assistance getting to the health center, hospital, and other health facilities",
    "Assist in repositioning in bed",
    "Supervise activity to prevent falls",
    "Monitor and assist in personal hygiene",
    "Assist in feeding",
];

export const Mobility = () => {
    return (
        <ScrollView>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            fontSize="$6"
                            fontWeight="bold"
                        >
                            Mobility Interventions
                        </Text>
                        <Text fontSize="$4">
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
            name="mobility"
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
    interventions: MobilityIntervention[];
    onChange: (
        interventions: MobilityIntervention[],
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
    const isSelected = !!existingIntervention;

    const toggleIntervention = (
        checked: boolean,
    ) => {
        if (checked) {
            const newIntervention: MobilityIntervention =
                {
                    id: `mobility_${Date.now()}_${Math.random()}`,
                    name: interventionName,
                    minutes: 0,
                    isCustom: false,
                };
            onChange([
                ...interventions,
                newIntervention,
            ]);
        } else {
            onChange(
                interventions.filter(
                    (i) =>
                        !(
                            i.name ===
                                interventionName &&
                            !i.isCustom
                        ),
                ),
            );
        }
    };

    const updateMinutes = (minutes: number) => {
        const updatedInterventions =
            interventions.map((i) =>
                i.name === interventionName &&
                !i.isCustom
                    ? { ...i, minutes }
                    : i,
            );
        onChange(updatedInterventions);
    };

    return (
        <YStack
            gap="$2"
            p="$3"
            borderWidth={1}
            borderColor="$green6"
            rounded="$4"
        >
            <XStack gap="$3">
                <Checkbox
                    checked={isSelected}
                    onCheckedChange={
                        toggleIntervention
                    }
                    size="$4"
                />
                <Text flex={1} fontSize="$4">
                    {interventionName}
                </Text>
            </XStack>

            {isSelected && (
                <XStack gap="$2" mt="$2">
                    <Text fontSize="$3">
                        Duration:
                    </Text>
                    <Input
                        flex={1}
                        value={
                            existingIntervention?.minutes?.toString() ||
                            ""
                        }
                        onChangeText={(text) => {
                            const numValue =
                                parseFloat(text);
                            updateMinutes(
                                isNaN(numValue)
                                    ? 0
                                    : numValue,
                            );
                        }}
                        placeholder="Minutes"
                        keyboardType="numeric"
                        size="$3"
                    />
                    <Text fontSize="$3">
                        minutes
                    </Text>
                </XStack>
            )}
        </YStack>
    );
};

const CustomIntervention = () => {
    const { control } = useCarePlanForm();
    const [customName, setCustomName] =
        useState("");
    const [
        customDescription,
        setCustomDescription,
    ] = useState("");

    return (
        <Controller
            control={control}
            name="mobility"
            render={({ field }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Custom Interventions
                    </Text>

                    {/* Show existing custom interventions */}
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

                    {/* Add new custom intervention */}
                    <YStack
                        gap="$2"
                        p="$3"
                        borderWidth={1}
                        borderColor="$green6"
                        rounded="$4"
                        borderStyle="dashed"
                    >
                        <Text
                            fontWeight="500"
                            fontSize="$4"
                        >
                            Add Custom
                            Intervention
                        </Text>

                        <Input
                            value={customName}
                            onChangeText={
                                setCustomName
                            }
                            placeholder="Intervention name"
                            size="$4"
                        />

                        <Input
                            value={
                                customDescription
                            }
                            onChangeText={
                                setCustomDescription
                            }
                            placeholder="Description (optional)"
                            multiline
                            numberOfLines={2}
                            textAlignVertical="top"
                            size="$4"
                        />

                        <Button
                            onPress={() => {
                                if (
                                    !customName.trim()
                                )
                                    return;

                                const newIntervention: MobilityIntervention =
                                    {
                                        id: `custom_mobility_${Date.now()}_${Math.random()}`,
                                        name: customName.trim(),
                                        minutes: 0,
                                        isCustom:
                                            true,
                                        description:
                                            customDescription.trim() ||
                                            undefined,
                                    };

                                field.onChange([
                                    ...(field.value ||
                                        []),
                                    newIntervention,
                                ]);
                                setCustomName("");
                                setCustomDescription(
                                    "",
                                );
                            }}
                            disabled={
                                !customName.trim()
                            }
                            size="$3"
                            self="flex-start"
                        >
                            <Ionicons
                                name="add"
                                size={16}
                            />
                            Add Intervention
                        </Button>
                    </YStack>
                </YStack>
            )}
        />
    );
};

interface CustomInterventionItemProps {
    intervention: MobilityIntervention;
    interventions: MobilityIntervention[];
    onChange: (
        interventions: MobilityIntervention[],
    ) => void;
}

const CustomInterventionItem = ({
    intervention,
    interventions,
    onChange,
}: CustomInterventionItemProps) => {
    const updateMinutes = (minutes: number) => {
        const updatedInterventions =
            interventions.map((i) =>
                i.id === intervention.id
                    ? { ...i, minutes }
                    : i,
            );
        onChange(updatedInterventions);
    };

    const removeIntervention = () => {
        onChange(
            interventions.filter(
                (i) => i.id !== intervention.id,
            ),
        );
    };

    return (
        <YStack
            gap="$2"
            p="$3"
            borderWidth={1}
            borderColor="$blue6"
            rounded="$4"
            bg="$blue2"
        >
            <XStack
                gap="$2"
                content="space-between"
            >
                <YStack flex={1}>
                    <Text
                        fontSize="$4"
                        fontWeight="500"
                    >
                        {intervention.name}
                    </Text>
                    {intervention.description && (
                        <Text fontSize="$3">
                            {
                                intervention.description
                            }
                        </Text>
                    )}
                </YStack>

                <Button
                    onPress={removeIntervention}
                    size="$2"
                    circular
                    variant="outlined"
                    color="$red10"
                >
                    <Ionicons
                        name="trash"
                        size={14}
                    />
                </Button>
            </XStack>

            <XStack gap="$2">
                <Text fontSize="$3">
                    Duration:
                </Text>
                <Input
                    flex={1}
                    value={
                        intervention.minutes?.toString() ||
                        ""
                    }
                    onChangeText={(text) => {
                        const numValue =
                            parseFloat(text);
                        updateMinutes(
                            isNaN(numValue)
                                ? 0
                                : numValue,
                        );
                    }}
                    placeholder="Minutes"
                    keyboardType="numeric"
                    size="$3"
                />
                <Text fontSize="$3">minutes</Text>
            </XStack>
        </YStack>
    );
};
