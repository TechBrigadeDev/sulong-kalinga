import { ScrollView } from "react-native";
import {
    Card,
    H4,
    Paragraph,
    YStack,
} from "tamagui";

interface EvaluationRecommendationsProps {
    evaluationRecommendations?: string;
}

export function EvaluationRecommendations({
    evaluationRecommendations,
}: EvaluationRecommendationsProps) {
    return (
        <Card bg="white" p="$4" space="$3">
            <YStack space="$3">
                <H4
                    color="#2c3e50"
                    fontWeight="600"
                >
                    💡 Evaluation and
                    Recommendations
                </H4>
                <Card
                    bg="#f8f9fa"
                    p="$3"
                    height={300}
                >
                    <ScrollView
                        showsVerticalScrollIndicator={
                            true
                        }
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
