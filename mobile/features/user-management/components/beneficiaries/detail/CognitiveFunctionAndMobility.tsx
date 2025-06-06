import { Card, H3, Text, XStack, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const CognitiveFunctionAndMobility = ({ beneficiary: _beneficiary }: Props) => {
    const cognitiveInfo = {
        Memory: "Rem necessitatibus quia eum.",
        "Thinking Skills": "Ratione nobis velit possimus quis.",
        Orientation: "Non ut doloribus possimus et repellendus in.",
        Behavior: "Eum aspernatur illum aut voluptas laborum perferendis.",
    };

    const mobilityInfo = {
        "Walking Ability": "Quibusdam voluptate aut veritatis velit dicta.",
        "Assistive Devices": "Nihil ipsum similique sequi modi error repudiandae unde.",
        "Transportation Needs": "Consequatur et in omnis mollitia voluptas eligendi.",
    };

    return (
        <XStack gap="$4" flexWrap="wrap">
            <Card elevate flex={1} minWidth={300}>
                <Card.Header padded>
                    <H3>Cognitive Function</H3>
                </Card.Header>
                <Card.Footer padded>
                    <YStack gap="$3">
                        {Object.entries(cognitiveInfo).map(([key, value]) => (
                            <YStack key={key}>
                                <Text opacity={0.6}>{key}</Text>
                                <Text>{value}</Text>
                            </YStack>
                        ))}
                    </YStack>
                </Card.Footer>
            </Card>

            <Card elevate flex={1} minWidth={300}>
                <Card.Header padded>
                    <H3>Mobility</H3>
                </Card.Header>
                <Card.Footer padded>
                    <YStack gap="$3">
                        {Object.entries(mobilityInfo).map(([key, value]) => (
                            <YStack key={key}>
                                <Text opacity={0.6}>{key}</Text>
                                <Text>{value}</Text>
                            </YStack>
                        ))}
                    </YStack>
                </Card.Footer>
            </Card>
        </XStack>
    );
};

export default CognitiveFunctionAndMobility;
