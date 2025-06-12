import {
    Activity,
    Camera,
    Droplet,
    Heart,
    Stethoscope,
    Thermometer,
} from "lucide-react-native";
import { Image } from "react-native";
import {
    Card,
    H6,
    Text,
    View,
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
                        Vital Signs & Photo
                        Documentation
                    </Text>
                </View>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <Stethoscope
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Vital Signs
                        </H6>
                    </XStack>

                    <Card bg="#f8f9fa" p="$3">
                        <YStack gap="$3">
                            <XStack
                                content="space-between"
                                items="center"
                            >
                                <XStack
                                    gap="$2"
                                    items="center"
                                >
                                    <Droplet
                                        size={16}
                                        color="#6c757d"
                                    />
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Blood
                                        Pressure
                                    </Text>
                                </XStack>
                                <XStack
                                    gap="$2"
                                    items="center"
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
                                content="space-between"
                                items="center"
                            >
                                <XStack
                                    gap="$2"
                                    items="center"
                                >
                                    <Thermometer
                                        size={16}
                                        color="#6c757d"
                                    />
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Temperature
                                    </Text>
                                </XStack>
                                <XStack
                                    gap="$2"
                                    items="center"
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
                                content="space-between"
                                items="center"
                            >
                                <XStack
                                    gap="$2"
                                    items="center"
                                >
                                    <Heart
                                        size={16}
                                        color="#6c757d"
                                    />
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Pulse Rate
                                    </Text>
                                </XStack>
                                <XStack
                                    gap="$2"
                                    items="center"
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
                                content="space-between"
                                items="center"
                            >
                                <XStack
                                    gap="$2"
                                    items="center"
                                >
                                    <Activity
                                        size={16}
                                        color="#6c757d"
                                    />
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Respiratory
                                        Rate
                                    </Text>
                                </XStack>
                                <XStack
                                    gap="$2"
                                    items="center"
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

                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <Camera
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Photo Documentation
                        </H6>
                    </XStack>
                    <Card
                        bg="#f8f9fa"
                        p="$3"
                        items="center"
                        content="center"
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
                                items="center"
                                gap="$2"
                            >
                                <Camera
                                    size={48}
                                    color="#6c757d"
                                />
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    No photo
                                    available
                                </Text>
                            </YStack>
                        )}
                    </Card>
                </YStack>
            </YStack>
        </Card>
    );
}
