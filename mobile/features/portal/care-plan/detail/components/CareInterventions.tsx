import type { Intervention } from "features/portal/care-plan/schema";
import {
    CheckCircle,
    Circle,
    Clock,
    HeartHandshake,
} from "lucide-react-native";
import React from "react";
import {
    Card,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface CareInterventionsProps {
    interventions?: Intervention[];
}

const InterventionItem: React.FC<{
    intervention: Intervention;
}> = ({ intervention }) => (
    <YStack
        gap="$2"
        paddingBlock="$3"
        borderBottomWidth={1}
        borderBottomColor="$borderColor"
    >
        <Text
            fontSize="$4"
            color="$color"
            fontWeight="500"
        >
            {intervention.intervention_description ||
                "No description"}
        </Text>

        <XStack items="center" gap="$3">
            {intervention.duration_minutes && (
                <XStack items="center" gap="$1">
                    <Clock
                        size={14}
                        color="grey"
                    />
                    <Text
                        fontSize="$3"
                        color="grey"
                    >
                        {
                            intervention.duration_minutes
                        }{" "}
                        min
                    </Text>
                </XStack>
            )}

            <XStack items="center" gap="$1">
                {intervention.implemented ? (
                    <CheckCircle
                        size={14}
                        color="green"
                    />
                ) : (
                    <Circle
                        size={14}
                        color="grey"
                    />
                )}
                <Text
                    fontSize="$3"
                    color={
                        intervention.implemented
                            ? "green"
                            : "grey"
                    }
                >
                    {intervention.implemented
                        ? "Completed"
                        : "Pending"}
                </Text>
            </XStack>
        </XStack>
    </YStack>
);

const CareInterventions: React.FC<
    CareInterventionsProps
> = ({ interventions = [] }) => {
    // Calculate total care time
    const totalMinutes = interventions.reduce(
        (total, intervention) => {
            return (
                total +
                parseFloat(
                    intervention.duration_minutes ||
                        "0",
                )
            );
        },
        0,
    );

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
                <XStack
                    items="center"
                    justify="space-between"
                >
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <HeartHandshake
                            size={20}
                            color="#3b82f6"
                        />
                        <Text
                            fontSize="$5"
                            fontWeight="600"
                            color="$color"
                        >
                            Care Interventions
                        </Text>
                    </XStack>
                    <Text
                        fontSize="$2"
                        color="grey"
                    >
                        {interventions.length}{" "}
                        total
                    </Text>
                </XStack>

                {interventions.length === 0 ? (
                    <Text
                        fontSize="$4"
                        color="grey"
                        fontStyle="italic"
                        paddingBlock="$2"
                    >
                        No interventions available
                    </Text>
                ) : (
                    <>
                        <YStack gap="$0">
                            {interventions.map(
                                (
                                    intervention,
                                    index,
                                ) => (
                                    <InterventionItem
                                        key={
                                            intervention.wcp_intervention_id ||
                                            index
                                        }
                                        intervention={
                                            intervention
                                        }
                                    />
                                ),
                            )}
                        </YStack>

                        {/* Total Care Time */}
                        <XStack
                            items="center"
                            justify="flex-end"
                            gap="$2"
                        >
                            <Clock
                                size={16}
                                color="#3b82f6"
                            />
                            <Text
                                fontSize="$5"
                                fontWeight="500"
                                color="#3b82f6"
                            >
                                Total Time:{" "}
                                <Text
                                    fontSize="$5"
                                    color="#3b82f6"
                                    fontWeight="700"
                                >
                                    {totalMinutes.toFixed(
                                        1,
                                    )}{" "}
                                </Text>
                                min
                            </Text>
                        </XStack>
                    </>
                )}
            </YStack>
        </Card>
    );
};

export default CareInterventions;
