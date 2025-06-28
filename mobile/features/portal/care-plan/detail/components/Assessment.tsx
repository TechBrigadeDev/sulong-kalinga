import {
    Circle,
    ClipboardList,
} from "lucide-react-native";
import React from "react";
import {
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface AssessmentProps {
    assessment?: string;
    illnesses?: string[];
}

const Assessment: React.FC<AssessmentProps> = ({
    assessment,
    illnesses = [],
}) => {
    return (
        <Card
            backgroundColor="$background"
            borderColor="$borderColor"
            borderWidth={1}
            borderRadius="$4"
            padding="$4"
            marginBottom="$3"
        >
            <YStack gap="$3">
                <XStack items="center" gap="$2">
                    <ClipboardList
                        size={20}
                        color="blue"
                    />
                    <Text
                        fontSize="$5"
                        fontWeight="600"
                        color="$color"
                    >
                        Assessment & Health
                        Conditions
                    </Text>
                </XStack>

                {assessment && (
                    <YStack gap="$2">
                        <Text
                            fontSize="$3"
                            fontWeight="500"
                            color="grey"
                        >
                            Assessment Notes
                        </Text>
                        <Text
                            fontSize="$4"
                            color="$color"
                            lineHeight="$1"
                        >
                            {assessment}
                        </Text>
                    </YStack>
                )}

                {illnesses.length > 0 && (
                    <YStack gap="$2">
                        <Text
                            fontSize="$3"
                            fontWeight="500"
                            color="grey"
                        >
                            Health Conditions
                        </Text>
                        <YStack gap="$1">
                            {illnesses.map(
                                (
                                    illness,
                                    index,
                                ) => (
                                    <XStack
                                        key={
                                            index
                                        }
                                        items="center"
                                        gap="$2"
                                    >
                                        <Circle
                                            size={
                                                6
                                            }
                                            color="$red10"
                                        />
                                        <Text
                                            fontSize="$4"
                                            color="$color"
                                            flex={
                                                1
                                            }
                                        >
                                            {
                                                illness
                                            }
                                        </Text>
                                    </XStack>
                                ),
                            )}
                        </YStack>
                    </YStack>
                )}

                {!assessment &&
                    illnesses.length === 0 && (
                        <Text
                            fontSize="$4"
                            color="grey"
                            fontStyle="italic"
                            text="center"
                            paddingBlock="$2"
                        >
                            No assessment
                            information available
                        </Text>
                    )}
            </YStack>
        </Card>
    );
};

export default Assessment;
