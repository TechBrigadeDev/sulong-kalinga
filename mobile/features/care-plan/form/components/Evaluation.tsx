import { log } from "common/debug";
import TabScroll from "components/tabs/TabScroll";
import { Image } from "expo-image";
import * as ImagePicker from "expo-image-picker";
import { useCarePlanForm } from "features/care-plan/form/form";
import { Image as LucideImage } from "lucide-react-native";
import { Controller } from "react-hook-form";
import {
    Button,
    Card,
    Input,
    Text,
    YStack,
} from "tamagui";

export interface EvaluationData {
    pictureUri: string | null;
    recommendations: string;
}

interface EvaluationProps {
    data?: EvaluationData;
    onChange?: (
        data: Partial<EvaluationData>,
    ) => void;
}

export const Evaluation = ({
    data: _data,
    onChange: _onChange,
}: EvaluationProps) => {
    return (
        <TabScroll>
            <YStack p="$4" gap="$4">
                <Card elevate>
                    <Card.Header padded>
                        <Text
                            fontSize="$6"
                            fontWeight="bold"
                        >
                            Evaluation
                        </Text>
                    </Card.Header>
                    <YStack p="$4" gap="$4">
                        <PictureUpload />
                        <RecommendationsInput />
                    </YStack>
                </Card>
            </YStack>
        </TabScroll>
    );
};

const PictureUpload = () => {
    const { control } = useCarePlanForm();

    const pickImage = async (
        onChange: (uri: string | null) => void,
    ) => {
        const result =
            await ImagePicker.launchImageLibraryAsync(
                {
                    mediaTypes:
                        ImagePicker
                            .MediaTypeOptions
                            .Images,
                    allowsEditing: true,
                    quality: 1,
                },
            );

        if (
            !result.canceled &&
            result.assets[0]
        ) {
            onChange(result.assets[0].uri);
        }
    };

    return (
        <Controller
            control={control}
            name="evaluation.pictureUri"
            render={({ field, fieldState }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Upload Picture
                    </Text>

                    <Button
                        theme="blue"
                        onPress={() =>
                            pickImage(
                                field.onChange,
                            )
                        }
                        icon={
                            <LucideImage
                                size={20}
                            />
                        }
                    >
                        {field.value
                            ? "Change Picture"
                            : "Select Picture"}
                    </Button>

                    {field.value && (
                        <YStack
                            gap="$2"
                            display="flex"
                        >
                            <Image
                                source={{
                                    uri: field.value,
                                }}
                                style={{
                                    width: "100%",
                                    height: 200,
                                    borderRadius: 8,
                                }}
                                contentFit="scale-down"
                                transition={100}
                            />
                            <Button
                                theme="red"
                                size="$3"
                                onPress={() =>
                                    field.onChange(
                                        null,
                                    )
                                }
                            >
                                Remove Picture
                            </Button>
                        </YStack>
                    )}

                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$4"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};

const RecommendationsInput = () => {
    const { control } = useCarePlanForm();

    return (
        <Controller
            control={control}
            name="evaluation.recommendations"
            render={({ field, fieldState }) => (
                <YStack gap="$3">
                    <Text
                        fontWeight="600"
                        fontSize="$5"
                    >
                        Recommendations
                    </Text>

                    <Input
                        placeholder="Enter evaluation recommendations"
                        value={field.value || ""}
                        onChangeText={
                            field.onChange
                        }
                        multiline
                        numberOfLines={4}
                        textAlignVertical="top"
                    />

                    {fieldState.error && (
                        <Text
                            color="$red10"
                            fontSize="$4"
                        >
                            {
                                fieldState.error
                                    .message
                            }
                        </Text>
                    )}
                </YStack>
            )}
        />
    );
};
