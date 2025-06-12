import { ScrollView } from "react-native";
import {
    Card,
    Paragraph,
    Text,
    View,
    YStack,
} from "tamagui";

interface EvaluationRecommendationsProps {
    evaluationRecommendations?: string;
}

export function EvaluationRecommendations({
    evaluationRecommendations,
}: EvaluationRecommendationsProps) {
    return (
        <Card bg="white" overflow="hidden">
            <Card.Header
                padded
                paddingBlock="$2"
                bg="#2d3748"
            >
                <View
                    display="flex"
                    flexDirection="row"
                    gap="$2"
                    items="center"
                    justify="center"
                >
                    <Text
                        color="white"
                        fontSize="$8"
                        fontWeight="bold"
                    >
                        Evaluation and
                        Recommendations
                    </Text>
                </View>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <Card
                    bg="#f8f9fa"
                    p="$3"
                    height={300}
                >
                    <ScrollView
                        showsVerticalScrollIndicator
                        nestedScrollEnabled
                    >
                        <Paragraph
                            fontSize="$4"
                            lineHeight="$5"
                            color="#495057"
                        >
                            {evaluationRecommendations ||
                                "Ang kalusugan at kalagayan ng beneficiary ay tuloy-tuloy na sinusubaybayan. Hinihikayat ang patuloy na pakikipag-ugnayan sa pamilya at mga kaibigan upang mapanatili ang mataas na antas ng kapakanan sa pang-araw-araw na pamumuhay. Mahalagang magpatuloy sa regular na paggamit ng mga prescribed na gamot at sumunod sa mga rekomendasyon ng doktor."}
                        </Paragraph>
                    </ScrollView>
                </Card>
            </YStack>
        </Card>
    );
}
