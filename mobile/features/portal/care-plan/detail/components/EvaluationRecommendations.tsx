import { Lightbulb } from "lucide-react-native";
import React from "react";
import {
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface EvaluationRecommendationsProps {
    evaluation?: string;
}

const EvaluationRecommendations: React.FC<
    EvaluationRecommendationsProps
> = ({ evaluation }) => {
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
                    <Lightbulb
                        size={20}
                        color="#eab308"
                    />
                    <Text
                        fontSize="$5"
                        fontWeight="600"
                        color="$color"
                    >
                        Evaluation &
                        Recommendations
                    </Text>
                </XStack>

                {evaluation ? (
                    <Text
                        fontSize="$4"
                        color="$color"
                        lineHeight="$1"
                    >
                        {evaluation}
                    </Text>
                ) : (
                    <Text
                        fontSize="$4"
                        color="grey"
                        fontStyle="italic"
                        text="center"
                        paddingBlock="$2"
                    >
                        No evaluation or
                        recommendations available
                    </Text>
                )}
            </YStack>
        </Card>
    );
};

export default EvaluationRecommendations;
