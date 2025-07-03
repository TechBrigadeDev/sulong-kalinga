import type { VitalSigns as VitalSignsType } from "features/portal/care-plan/schema";
import {
    Activity,
    icons,
} from "lucide-react-native";
import React from "react";
import {
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface VitalSignsProps {
    vitalSigns?: VitalSignsType;
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
    iconColor = "#3b82f6",
}) => (
    <XStack
        items="center"
        justify="space-between"
        py="$2"
        borderBottomWidth={1}
        borderBottomColor="$borderColor"
    >
        <XStack items="center" gap="$2" flex={1}>
            {(() => {
                const IconComponent = icons[icon];
                return (
                    <IconComponent
                        size={18}
                        color={iconColor}
                    />
                );
            })()}
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
    vitalSigns,
}) => {
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
                        color="#10b981"
                    />
                    <Text
                        fontSize="$5"
                        fontWeight="600"
                        color="$color"
                    >
                        Vital Signs
                    </Text>
                </XStack>

                {vitalSigns ? (
                    <YStack gap="$1">
                        <VitalSignItem
                            icon="Heart"
                            label="Blood Pressure"
                            value={
                                vitalSigns.blood_pressure ||
                                undefined
                            }
                            iconColor="#ef4444"
                        />
                        <VitalSignItem
                            icon="Thermometer"
                            label="Temperature"
                            value={
                                vitalSigns.body_temperature ||
                                undefined
                            }
                            unit="°C"
                            iconColor="#f97316"
                        />
                        <VitalSignItem
                            icon="Activity"
                            label="Pulse Rate"
                            value={
                                vitalSigns.pulse_rate ||
                                undefined
                            }
                            unit="bpm"
                            iconColor="#3b82f6"
                        />
                        <VitalSignItem
                            icon="Wind"
                            label="Respiratory Rate"
                            value={
                                vitalSigns.respiratory_rate ||
                                undefined
                            }
                            unit="rpm"
                            iconColor="#10b981"
                        />

                        {vitalSigns.created_at && (
                            <Text
                                fontSize="$2"
                                color="grey"
                                mt="$2"
                            >
                                Recorded:{" "}
                                {new Date(
                                    vitalSigns.created_at,
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
                        py="$2"
                    >
                        No vital signs recorded
                    </Text>
                )}
            </YStack>
        </Card>
    );
};

export default VitalSigns;
