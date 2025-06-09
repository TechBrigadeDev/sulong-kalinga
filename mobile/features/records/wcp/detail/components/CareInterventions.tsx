import Badge from "components/Bagde";
import {
    Card,
    H4,
    H6,
    Text,
    XStack,
    YStack,
} from "tamagui";

interface Intervention {
    wcp_intervention_id: number;
    intervention_id: number | null;
    care_category_id: number;
    intervention_description: string | null;
    duration_minutes: string;
    implemented: boolean;
}

interface CareInterventionsProps {
    interventions: Intervention[];
}

export function CareInterventions({
    interventions,
}: CareInterventionsProps) {
    const categoryMap: Record<
        number,
        { name: string; icon: string }
    > = {
        1: { name: "Personal Care", icon: "🛁" },
        2: {
            name: "Health Monitoring",
            icon: "🩺",
        },
        3: {
            name: "Medication Management",
            icon: "💊",
        },
        4: {
            name: "Mobility Support",
            icon: "🚶",
        },
        5: {
            name: "Social Engagement",
            icon: "👥",
        },
        6: {
            name: "Safety Measures",
            icon: "🛡️",
        },
    };

    return (
        <Card bg="white" p="$4" space="$3">
            <YStack space="$3">
                <H4
                    color="#2c3e50"
                    fontWeight="600"
                >
                    🎯 Care Interventions (
                    {interventions.length} items)
                </H4>

                <YStack space="$3">
                    {interventions.map(
                        (intervention) => {
                            const category =
                                categoryMap[
                                    intervention
                                        .care_category_id
                                ];
                            return (
                                <Card
                                    key={
                                        intervention.wcp_intervention_id
                                    }
                                    bg="#f8f9fa"
                                    p="$3"
                                >
                                    <YStack space="$2">
                                        <XStack space="$2">
                                            <Text fontSize="$4">
                                                {category?.icon ||
                                                    "🔧"}
                                            </Text>
                                            <YStack
                                                space="$1"
                                                flex={
                                                    1
                                                }
                                            >
                                                <H6 color="#495057">
                                                    {category?.name ||
                                                        "General Care"}
                                                </H6>
                                                <Text
                                                    fontSize="$4"
                                                    color="#495057"
                                                >
                                                    {intervention.intervention_description ||
                                                        `${category?.name || "General"} intervention`}
                                                </Text>
                                            </YStack>
                                            <Badge
                                                variant={
                                                    intervention.implemented
                                                        ? "success"
                                                        : "warning"
                                                }
                                            >
                                                {intervention.implemented
                                                    ? "✓ Done"
                                                    : "⏳ Pending"}
                                            </Badge>
                                        </XStack>

                                        <XStack space="$2">
                                            <Text fontSize="$2">
                                                ⏱️
                                            </Text>
                                            <Text
                                                fontSize="$3"
                                                color="#6c757d"
                                            >
                                                Duration:{" "}
                                                {
                                                    intervention.duration_minutes
                                                }{" "}
                                                minutes
                                            </Text>
                                        </XStack>
                                    </YStack>
                                </Card>
                            );
                        },
                    )}
                </YStack>
            </YStack>
        </Card>
    );
}
