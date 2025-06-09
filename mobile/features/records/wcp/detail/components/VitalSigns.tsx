import { Image } from "react-native";
import {
    Card,
    H4,
    H6,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface VitalSignsProps {
    vitalSigns: {
        blood_pressure: string;
        temperature: string;
        pulse: string;
        respiratory_rate: string;
    };
    photoUrl: string;
}

export function VitalSigns({
    vitalSigns,
    photoUrl,
}: VitalSignsProps) {
    const getVitalStatus = (
        type: string,
        value: string,
    ) => {
        // Simple status logic
        return {
            color: "#28a745", // Default to green (normal)
            status: "Normal",
        };
    };

    return (
        <Card bg="white" p="$4" space="$3">
            <YStack space="$2">
                <H4
                    color="#2c3e50"
                    fontWeight="600"
                >
                    💓 Vital Signs & Photo
                    Documentation
                </H4>
            </YStack>

            <XStack space="$3">
                <YStack space="$3" flex={1}>
                    <H6 color="#495057">
                        📊 Vital Signs
                    </H6>

                    <Card bg="#f8f9fa" p="$3">
                        <YStack space="$3">
                            <XStack
                                jc="space-between"
                                ai="center"
                            >
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text fontSize="$2">
                                        🩸
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Blood
                                        Pressure
                                    </Text>
                                </XStack>
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text
                                        fontSize="$5"
                                        fontWeight="bold"
                                        color="#28a745"
                                    >
                                        {
                                            vitalSigns.blood_pressure
                                        }
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        mmHg
                                    </Text>
                                </XStack>
                            </XStack>

                            <XStack
                                jc="space-between"
                                ai="center"
                            >
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text fontSize="$2">
                                        🌡️
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Temperature
                                    </Text>
                                </XStack>
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text
                                        fontSize="$5"
                                        fontWeight="bold"
                                        color="#28a745"
                                    >
                                        {
                                            vitalSigns.temperature
                                        }
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        °C
                                    </Text>
                                </XStack>
                            </XStack>

                            <XStack
                                jc="space-between"
                                ai="center"
                            >
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text fontSize="$2">
                                        💓
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Pulse Rate
                                    </Text>
                                </XStack>
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text
                                        fontSize="$5"
                                        fontWeight="bold"
                                        color="#28a745"
                                    >
                                        {
                                            vitalSigns.pulse
                                        }
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        bpm
                                    </Text>
                                </XStack>
                            </XStack>

                            <XStack
                                jc="space-between"
                                ai="center"
                            >
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text fontSize="$2">
                                        🫁
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Respiratory
                                        Rate
                                    </Text>
                                </XStack>
                                <XStack
                                    space="$2"
                                    ai="center"
                                >
                                    <Text
                                        fontSize="$5"
                                        fontWeight="bold"
                                        color="#28a745"
                                    >
                                        {
                                            vitalSigns.respiratory_rate
                                        }
                                    </Text>
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        bpm
                                    </Text>
                                </XStack>
                            </XStack>
                        </YStack>
                    </Card>
                </YStack>

                <YStack space="$3" flex={1}>
                    <H6 color="#495057">
                        📸 Photo Documentation
                    </H6>
                    <Card
                        bg="#f8f9fa"
                        p="$3"
                        ai="center"
                        jc="center"
                        minHeight={250}
                    >
                        {photoUrl ? (
                            <Image
                                source={{
                                    uri: photoUrl,
                                }}
                                style={{
                                    width: 200,
                                    height: 200,
                                    borderRadius: 8,
                                }}
                                resizeMode="cover"
                            />
                        ) : (
                            <YStack
                                ai="center"
                                space="$2"
                            >
                                <Text fontSize="$6">
                                    📷
                                </Text>
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                    ta="center"
                                >
                                    No photo
                                    available
                                </Text>
                            </YStack>
                        )}
                    </Card>
                </YStack>
            </XStack>
        </Card>
    );
}
