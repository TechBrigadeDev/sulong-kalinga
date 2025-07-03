import {
    Accessibility,
    Brain,
} from "lucide-react-native";
import {
    Card,
    H3,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const CognitiveFunctionAndMobility = ({
    beneficiary: _beneficiary,
}: Props) => {
    const cognitiveInfo = {
        Memory: "Rem necessitatibus quia eum.",
        "Thinking Skills":
            "Ratione nobis velit possimus quis.",
        Orientation:
            "Non ut doloribus possimus et repellendus in.",
        Behavior:
            "Eum aspernatur illum aut voluptas laborum perferendis.",
    };

    const mobilityInfo = {
        "Walking Ability":
            "Quibusdam voluptate aut veritatis velit dicta.",
        "Assistive Devices":
            "Nihil ipsum similique sequi modi error repudiandae unde.",
        "Transportation Needs":
            "Consequatur et in omnis mollitia voluptas eligendi.",
    };

    return (
        <YStack gap="$4" mb="$4">
            <Card
                elevate
                style={{ borderRadius: 16 }}
                backgroundColor="$background"
                borderColor="$borderColor"
            >
                <YStack gap="$4" p="$4">
                    <XStack
                        gap="$3"
                        style={{
                            alignItems: "center",
                        }}
                    >
                        <Brain
                            size={24}
                            color="#8b5cf6"
                        />
                        <H3
                            color="#111827"
                            fontWeight="600"
                        >
                            Cognitive Function
                        </H3>
                    </XStack>
                    <YStack gap="$3">
                        {Object.entries(
                            cognitiveInfo,
                        ).map(([key, value]) => (
                            <YStack key={key}>
                                <Text
                                    fontSize="$3"
                                    style={{
                                        color: "#6b7280",
                                    }}
                                    fontWeight="500"
                                    mb="$2"
                                >
                                    {key}
                                </Text>
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: "#111827",
                                    }}
                                    fontWeight="400"
                                >
                                    {value}
                                </Text>
                            </YStack>
                        ))}
                    </YStack>
                </YStack>
            </Card>

            <Card
                elevate
                style={{ borderRadius: 16 }}
                backgroundColor="$background"
                borderColor="$borderColor"
            >
                <YStack gap="$4" p="$4">
                    <XStack
                        gap="$3"
                        style={{
                            alignItems: "center",
                        }}
                    >
                        <Accessibility
                            size={24}
                            color="#059669"
                        />
                        <H3
                            color="#111827"
                            fontWeight="600"
                        >
                            Mobility &
                            Transportation
                        </H3>
                    </XStack>
                    <YStack gap="$3">
                        {Object.entries(
                            mobilityInfo,
                        ).map(([key, value]) => (
                            <YStack key={key}>
                                <Text
                                    fontSize="$3"
                                    style={{
                                        color: "#6b7280",
                                    }}
                                    fontWeight="500"
                                    mb="$2"
                                >
                                    {key}
                                </Text>
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: "#111827",
                                    }}
                                    fontWeight="400"
                                >
                                    {value}
                                </Text>
                            </YStack>
                        ))}
                    </YStack>
                </YStack>
            </Card>
        </YStack>
    );
};

export default CognitiveFunctionAndMobility;
