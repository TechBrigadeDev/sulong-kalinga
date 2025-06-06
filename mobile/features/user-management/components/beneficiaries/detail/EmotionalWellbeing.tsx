import { Card, H3, Text, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const EmotionalWellbeing = ({ beneficiary: _beneficiary }: Props) => {
    const emotionalInfo = {
        Mood: "Expedita sint nemo quaerat qui dignissimos consequatur consequatur.",
        "Social Interactions": "Quas similique est voluptatum est sunt similique.",
        "Emotional Support Need": "Nam cumque et delectus qui tempore nam.",
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Emotional Well-being</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    {Object.entries(emotionalInfo).map(([key, value]) => (
                        <YStack key={key}>
                            <Text opacity={0.6}>{key}</Text>
                            <Text>{value}</Text>
                        </YStack>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default EmotionalWellbeing;
