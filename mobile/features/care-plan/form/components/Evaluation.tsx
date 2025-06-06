import TabScroll from "components/tabs/TabScroll";
import * as ImagePicker from "expo-image-picker";
import { Image as LucideImage } from "lucide-react-native";
import { Button, Card, Input, Text, YStack } from "tamagui";

export interface EvaluationData {
    pictureUri: string | null;
    recommendations: string;
}

interface EvaluationProps {
    data: EvaluationData;
    onChange: (data: Partial<EvaluationData>) => void;
}

export const Evaluation = ({
    data = {
        pictureUri: null,
        recommendations: "",
    },
    onChange,
}: EvaluationProps) => {
    const pickImage = async () => {
        const result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [4, 3],
            quality: 1,
        });

        if (!result.canceled && result.assets[0]) {
            onChange({ pictureUri: result.assets[0].uri });
        }
    };

    return (
        <TabScroll>
            <YStack gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text size="$6" fontWeight="bold">
                            Upload Picture
                        </Text>
                    </Card.Header>
                    <YStack gap="$4">
                        <Button onPress={pickImage} theme="blue" icon={<LucideImage size={16} />}>
                            Choose Picture
                        </Button>
                        {/* {data.pictureUri ? (
              <Image
                source={{ uri: data.pictureUri }}
                width="100%"
                height={200}
                resizeMode="contain"
              />
            ) : (
              <Text color="$gray11">No image selected</Text>
            )} */}
                    </YStack>
                </Card>

                <Card elevate>
                    <Card.Header padded>
                        <Text size="$6" fontWeight="bold">
                            Recommendations and Evaluations
                        </Text>
                    </Card.Header>
                    <Card.Footer padded>
                        <Input
                            multiline
                            numberOfLines={6}
                            textAlignVertical="top"
                            value={data.recommendations}
                            onChangeText={(text) => onChange({ recommendations: text })}
                            placeholder="Enter your recommendations and evaluations here..."
                        />
                    </Card.Footer>
                </Card>
            </YStack>
        </TabScroll>
    );
};
