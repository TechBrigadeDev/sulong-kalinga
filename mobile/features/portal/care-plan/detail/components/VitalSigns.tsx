import type { VitalSigns as VitalSignsType } from "features/portal/care-plan/schema";
import {
    Activity,
    icons,
} from "lucide-react-native";
import React from "react";
import {
    Card,
    Image,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface VitalSignsProps {
    vitalSigns?: VitalSignsType[];
}

const VitalSignItem: React.FC<{
    icon: keyof typeof icons;
    label: string;
    value?: string | number;
    unit?: string;
    iconColor?: string;
}> = ({
    icon,
    label,
    value,
    unit,
    iconColor = "$blue10",
}) => (
    <XStack
        items="center"
        justify="space-between"
        paddingBlock="$2"
        borderBottomWidth={1}
        borderBottomColor="$borderColor"
    >
        <XStack items="center" gap="$2" flex={1}>
            {() => {
                const IconComponent = icons[icon];
                return (
                    <IconComponent
                        size={18}
                        color={iconColor}
                    />
                );
            }}
            <Text
                fontSize="$4"
                color="$color"
                fontWeight="500"
            >
                {label}
            </Text>
        </XStack>
        <Text
            fontSize="$4"
            color="$color"
            fontWeight="600"
        >
            {value
                ? `${value}${unit ? ` ${unit}` : ""}`
                : "Not recorded"}
        </Text>
    </XStack>
);

const VitalSigns: React.FC<VitalSignsProps> = ({
    vitalSigns = [],
}) => {
    // Get the most recent vital signs
    const latestVitalSigns =
        vitalSigns[vitalSigns.length - 1];

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
                    <Activity
                        size={20}
                        color="$green10"
                    />
                    <Text
                        fontSize="$5"
                        fontWeight="600"
                        color="$color"
                    >
                        Vital Signs
                    </Text>
                </XStack>

                {latestVitalSigns ? (
                    <YStack gap="$1">
                        <VitalSignItem
                            icon="Heart"
                            label="Blood Pressure"
                            value={
                                latestVitalSigns.blood_pressure ||
                                undefined
                            }
                            iconColor="$red10"
                        />
                        <VitalSignItem
                            icon="Thermometer"
                            label="Temperature"
                            value={
                                latestVitalSigns.temperature ||
                                undefined
                            }
                            unit="°C"
                            iconColor="$orange10"
                        />
                        <VitalSignItem
                            icon="Activity"
                            label="Pulse Rate"
                            value={
                                latestVitalSigns.pulse_rate ||
                                undefined
                            }
                            unit="bpm"
                            iconColor="$blue10"
                        />
                        <VitalSignItem
                            icon="Wind"
                            label="Respiratory Rate"
                            value={
                                latestVitalSigns.respiratory_rate ||
                                undefined
                            }
                            unit="rpm"
                            iconColor="$green10"
                        />

                        {latestVitalSigns.photo_documentation && (
                            <YStack
                                gap="$2"
                                marginBlockStart="$2"
                            >
                                <Text
                                    fontSize="$3"
                                    fontWeight="500"
                                    color={"grey"}
                                >
                                    Photo
                                    Documentation
                                </Text>
                                <Card
                                    borderWidth={
                                        1
                                    }
                                    borderColor="$borderColor"
                                    borderRadius="$3"
                                    overflow="hidden"
                                >
                                    <Image
                                        source={{
                                            uri: latestVitalSigns.photo_documentation,
                                        }}
                                        width="100%"
                                        height={
                                            200
                                        }
                                        resizeMode="cover"
                                    />
                                </Card>
                            </YStack>
                        )}

                        {latestVitalSigns.recorded_at && (
                            <Text
                                fontSize="$2"
                                color="grey"
                                marginBlockStart="$2"
                                text="right"
                            >
                                Recorded:{" "}
                                {new Date(
                                    latestVitalSigns.recorded_at,
                                ).toLocaleDateString(
                                    "en-US",
                                    {
                                        year: "numeric",
                                        month: "short",
                                        day: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    },
                                )}
                            </Text>
                        )}
                    </YStack>
                ) : (
                    <Text
                        fontSize="$4"
                        color="grey"
                        fontStyle="italic"
                        text="center"
                        paddingBlock="$2"
                    >
                        No vital signs recorded
                    </Text>
                )}

                {vitalSigns.length > 1 && (
                    <Text
                        fontSize="$2"
                        color="$blue10"
                        text="center"
                        marginBlockStart="$2"
                    >
                        {vitalSigns.length}{" "}
                        records available •
                        Showing latest
                    </Text>
                )}
            </YStack>
        </Card>
    );
};

export default VitalSigns;
