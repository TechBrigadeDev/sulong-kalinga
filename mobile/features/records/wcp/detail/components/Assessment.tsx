import { ScrollView } from "react-native";
import {
    Card,
    H4,
    Paragraph,
    YStack,
} from "tamagui";

interface AssessmentProps {
    assessment?: string;
}

const Assessment = ({
    assessment,
}: AssessmentProps) => {
    return (
        <Card elevate bordered p="$4" mb="$3">
            <YStack space="$3">
                <H4
                    color="#2c3e50"
                    fontWeight="600"
                >
                    🔍 Assessment
                </H4>
                <Card
                    bg="#f8f9fa"
                    p="$3"
                    maxHeight={300}
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
                            {assessment ||
                                "No assessment notes available"}
                        </Paragraph>
                    </ScrollView>
                </Card>
            </YStack>
        </Card>
    );
};

export default Assessment;
