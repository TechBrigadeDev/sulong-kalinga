import { Ionicons } from "@expo/vector-icons";
import { useState } from "react";
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

export interface SocialContactIntervention {
    id: string;
    name: string;
    minutes: string;
    isCustom?: boolean;
}

const DEFAULT_INTERVENTIONS: Omit<
    SocialContactIntervention,
    "id"
>[] = [
    {
        name: "Assist in attending Senior Citizens activities",
        minutes: "",
    },
    {
        name: "Assist in going to the Senior Citizens Day Center",
        minutes: "",
    },
    {
        name: "Encourage visits from family, friends, or caregivers to provide companionship and emotional support",
        minutes: "",
    },
    {
        name: "Arrange regular phone calls or other communication mediums with loved ones to maintain social connections",
        minutes: "",
    },
    {
        name: "Connect with support groups for individuals with chronic diseases",
        minutes: "",
    },
];

export interface SocialContactData {
    interventions: SocialContactIntervention[];
}

interface SocialContactProps {
    data: SocialContactData;
    onChange: (
        data: Partial<SocialContactData>,
    ) => void;
}

export const SocialContact = ({
    data,
    onChange,
}: SocialContactProps) => {
    const [
        customIntervention,
        setCustomIntervention,
    ] = useState("");

    const toggleIntervention = (
        intervention: SocialContactIntervention,
    ) => {
        const newInterventions =
            data.interventions.some(
                (i) => i.id === intervention.id,
            )
                ? data.interventions.filter(
                      (i) =>
                          i.id !==
                          intervention.id,
                  )
                : [
                      ...data.interventions,
                      intervention,
                  ];

        onChange({
            interventions: newInterventions,
        });
    };

    const updateMinutes = (
        id: string,
        minutes: string,
    ) => {
        const newInterventions =
            data.interventions.map((i) =>
                i.id === id
                    ? { ...i, minutes }
                    : i,
            );
        onChange({
            interventions: newInterventions,
        });
    };

    const addCustomIntervention = () => {
        if (!customIntervention.trim()) return;

        const newIntervention: SocialContactIntervention =
            {
                id: Date.now().toString(),
                name: customIntervention,
                minutes: "",
                isCustom: true,
            };

        onChange({
            interventions: [
                ...data.interventions,
                newIntervention,
            ],
        });

        setCustomIntervention("");
    };

    const removeIntervention = (id: string) => {
        onChange({
            interventions:
                data.interventions.filter(
                    (i) => i.id !== id,
                ),
        });
    };

    return (
        <ScrollView>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            size="$6"
                            fontWeight="bold"
                        >
                            Social Contact
                            Interventions
                        </Text>
                    </Card.Header>
                    <YStack p="$4">
                        <YStack gap="$4">
                            {DEFAULT_INTERVENTIONS.map(
                                (
                                    intervention,
                                    index,
                                ) => {
                                    const savedIntervention =
                                        data.interventions.find(
                                            (i) =>
                                                i.name ===
                                                intervention.name,
                                        );
                                    const checked =
                                        Boolean(
                                            savedIntervention,
                                        );

                                    return (
                                        <XStack
                                            key={
                                                index
                                            }
                                            gap="$4"
                                            alignItems="center"
                                        >
                                            <Checkbox
                                                checked={
                                                    checked
                                                }
                                                onCheckedChange={() =>
                                                    toggleIntervention(
                                                        {
                                                            id:
                                                                savedIntervention?.id ||
                                                                Date.now().toString(),
                                                            ...intervention,
                                                        },
                                                    )
                                                }
                                                size="$4"
                                            >
                                                <Checkbox.Indicator>
                                                    <Ionicons
                                                        name="checkmark"
                                                        size={
                                                            16
                                                        }
                                                    />
                                                </Checkbox.Indicator>
                                            </Checkbox>

                                            <YStack
                                                flex={
                                                    1
                                                }
                                            >
                                                <Text>
                                                    {
                                                        intervention.name
                                                    }
                                                </Text>
                                                {checked && (
                                                    <XStack
                                                        gap="$2"
                                                        marginTop="$2"
                                                        alignItems="center"
                                                    >
                                                        <Input
                                                            flex={
                                                                1
                                                            }
                                                            placeholder="Minutes"
                                                            keyboardType="numeric"
                                                            value={
                                                                savedIntervention?.minutes
                                                            }
                                                            onChangeText={(
                                                                text,
                                                            ) =>
                                                                updateMinutes(
                                                                    savedIntervention!
                                                                        .id,
                                                                    text,
                                                                )
                                                            }
                                                        />
                                                        <Text>
                                                            min
                                                        </Text>
                                                    </XStack>
                                                )}
                                            </YStack>
                                        </XStack>
                                    );
                                },
                            )}

                            {data.interventions
                                .filter(
                                    (i) =>
                                        i.isCustom,
                                )
                                .map(
                                    (
                                        intervention,
                                    ) => (
                                        <XStack
                                            key={
                                                intervention.id
                                            }
                                            gap="$4"
                                            alignItems="center"
                                        >
                                            <Checkbox
                                                checked={
                                                    true
                                                }
                                                onCheckedChange={() =>
                                                    removeIntervention(
                                                        intervention.id,
                                                    )
                                                }
                                                size="$4"
                                            >
                                                <Checkbox.Indicator>
                                                    <Ionicons
                                                        name="checkmark"
                                                        size={
                                                            16
                                                        }
                                                    />
                                                </Checkbox.Indicator>
                                            </Checkbox>

                                            <YStack
                                                flex={
                                                    1
                                                }
                                            >
                                                <Text>
                                                    {
                                                        intervention.name
                                                    }
                                                </Text>
                                                <XStack
                                                    gap="$2"
                                                    marginTop="$2"
                                                    alignItems="center"
                                                >
                                                    <Input
                                                        flex={
                                                            1
                                                        }
                                                        placeholder="Minutes"
                                                        keyboardType="numeric"
                                                        value={
                                                            intervention.minutes
                                                        }
                                                        onChangeText={(
                                                            text,
                                                        ) =>
                                                            updateMinutes(
                                                                intervention.id,
                                                                text,
                                                            )
                                                        }
                                                    />
                                                    <Text>
                                                        min
                                                    </Text>
                                                </XStack>
                                            </YStack>

                                            <Button
                                                theme="red"
                                                onPress={() =>
                                                    removeIntervention(
                                                        intervention.id,
                                                    )
                                                }
                                                icon={
                                                    <Ionicons
                                                        name="trash-outline"
                                                        size={
                                                            16
                                                        }
                                                    />
                                                }
                                            />
                                        </XStack>
                                    ),
                                )}

                            <YStack gap="$2">
                                <XStack gap="$2">
                                    <Input
                                        flex={1}
                                        placeholder="Enter custom social contact intervention"
                                        value={
                                            customIntervention
                                        }
                                        onChangeText={
                                            setCustomIntervention
                                        }
                                    />
                                    <Button
                                        theme="blue"
                                        onPress={
                                            addCustomIntervention
                                        }
                                        icon={
                                            <Ionicons
                                                name="add-outline"
                                                size={
                                                    16
                                                }
                                            />
                                        }
                                    >
                                        Add
                                    </Button>
                                </XStack>
                            </YStack>
                        </YStack>
                    </YStack>
                </Card>
            </YStack>
        </ScrollView>
    );
};
