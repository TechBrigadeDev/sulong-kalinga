import {
    Card,
    Paragraph,
    ScrollView,
    Text,
    View,
    YStack,
} from "tamagui";

interface AssessmentProps {
    assessment?: string;
}

const Assessment = ({
    assessment,
}: AssessmentProps) => {
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
                        Assessment
                    </Text>
                </View>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <Card
                    bg="#f8f9fa"
                    p="$3"
                    maxHeight={300}
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
