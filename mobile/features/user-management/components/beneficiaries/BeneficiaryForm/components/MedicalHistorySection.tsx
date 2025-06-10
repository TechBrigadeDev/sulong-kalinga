import {
    Card,
    H3,
    Input,
    Text,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    data?: Partial<IBeneficiary>;
    onChange?: (
        field: string | number | symbol,
        value: any,
    ) => void;
}

export const MedicalHistorySection = ({
    data = {},
    onChange = () => {},
}: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medical History</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <YStack gap="$2">
                    <Text fontWeight="600">
                        Medical Conditions
                    </Text>
                    <Input
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                        value={
                            data?.medical_conditions
                        }
                        onChangeText={(value) =>
                            onChange(
                                "medical_conditions",
                                value,
                            )
                        }
                        placeholder="List all medical conditions"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Separate multiple
                        conditions with commas
                    </Text>
                </YStack>

                <YStack gap="$2">
                    <Text fontWeight="600">
                        Medications
                    </Text>
                    <Input
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                        value={data?.medications}
                        onChangeText={(value) =>
                            onChange(
                                "medications",
                                value,
                            )
                        }
                        placeholder="List all medications"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Separate multiple
                        medications with commas
                    </Text>
                </YStack>

                <YStack gap="$2">
                    <Text fontWeight="600">
                        Allergies
                    </Text>
                    <Input
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                        value={data?.allergies}
                        onChangeText={(value) =>
                            onChange(
                                "allergies",
                                value,
                            )
                        }
                        placeholder="List all allergies"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Separate multiple
                        allergies with commas
                    </Text>
                </YStack>

                <YStack gap="$2">
                    <Text fontWeight="600">
                        Immunizations
                    </Text>
                    <Input
                        multiline
                        numberOfLines={3}
                        textAlignVertical="top"
                        value={
                            data?.immunizations
                        }
                        onChangeText={(value) =>
                            onChange(
                                "immunizations",
                                value,
                            )
                        }
                        placeholder="List all immunizations"
                    />
                    <Text
                        opacity={0.6}
                        fontSize="$2"
                    >
                        Separate multiple
                        immunizations with commas
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};
