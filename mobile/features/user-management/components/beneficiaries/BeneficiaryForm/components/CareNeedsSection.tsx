import { Card, H3, Input, Text, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (field: string | number | symbol, value: any) => void;
}

interface CareNeed {
    label: string;
    frequencyField: string;
    assistanceField: string;
}

const CARE_NEEDS: CareNeed[] = [
    {
        label: "Mobility",
        frequencyField: "mobility_frequency",
        assistanceField: "mobility_assistance",
    },
    {
        label: "Cognitive / Communication",
        frequencyField: "cognitive_frequency",
        assistanceField: "cognitive_assistance",
    },
    {
        label: "Self-sustainability",
        frequencyField: "self_sustainability_frequency",
        assistanceField: "self_sustainability_assistance",
    },
    {
        label: "Disease / Therapy Handling",
        frequencyField: "disease_therapy_frequency",
        assistanceField: "disease_therapy_assistance",
    },
    {
        label: "Daily Life / Social Contact",
        frequencyField: "daily_life_frequency",
        assistanceField: "daily_life_assistance",
    },
    {
        label: "Outdoor Activities",
        frequencyField: "outdoor_frequency",
        assistanceField: "outdoor_assistance",
    },
    {
        label: "Household Keeping",
        frequencyField: "household_frequency",
        assistanceField: "household_assistance",
    },
];

export const CareNeedsSection = ({ data = {}, onChange = () => {} }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Care Needs</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$4">
                    {CARE_NEEDS.map((need, index) => (
                        <YStack key={index} gap="$2">
                            <Text size="$5" fontWeight="bold">
                                {need.label}
                            </Text>
                            <YStack gap="$2">
                                <Text>Frequency</Text>
                                <Input
                                    multiline
                                    numberOfLines={2}
                                    textAlignVertical="top"
                                    value={
                                        data[need.frequencyField as keyof IBeneficiary] as string
                                    }
                                    onChangeText={(value) =>
                                        onChange(need.frequencyField as keyof IBeneficiary, value)
                                    }
                                    placeholder="Enter frequency"
                                />
                            </YStack>
                            <YStack gap="$2">
                                <Text>Assistance Required</Text>
                                <Input
                                    multiline
                                    numberOfLines={2}
                                    textAlignVertical="top"
                                    value={
                                        data[need.assistanceField as keyof IBeneficiary] as string
                                    }
                                    onChangeText={(value) =>
                                        onChange(need.assistanceField as keyof IBeneficiary, value)
                                    }
                                    placeholder="Enter assistance required"
                                />
                            </YStack>
                        </YStack>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};
