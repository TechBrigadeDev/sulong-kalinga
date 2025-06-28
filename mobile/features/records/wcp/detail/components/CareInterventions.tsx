import Badge from "components/Bagde";
import {
    Bath,
    CheckCircle,
    Clock3,
    Heart,
    Pill,
    Shield,
    Stethoscope,
    Users,
} from "lucide-react-native";
import {
    Card,
    Text,
    View,
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
        { name: string; IconComponent: any }
    > = {
        1: {
            name: "PERSONAL CARE",
            IconComponent: Bath,
        },
        2: {
            name: "HEALTH MONITORING",
            IconComponent: Stethoscope,
        },
        3: {
            name: "MEDICATION MANAGEMENT",
            IconComponent: Pill,
        },
        4: {
            name: "MOBILITY SUPPORT",
            IconComponent: Heart,
        },
        5: {
            name: "SOCIAL ENGAGEMENT",
            IconComponent: Users,
        },
        6: {
            name: "SAFETY MEASURES",
            IconComponent: Shield,
        },
        // Add mappings for the categories shown in web UI
        7: {
            name: "COGNITIVE/COMMUNICATION",
            IconComponent: Users,
        },
        8: {
            name: "DISEASE/THERAPY HANDLING",
            IconComponent: Pill,
        },
    };

    // Group interventions by category
    const groupedInterventions =
        interventions.reduce(
            (acc, intervention) => {
                const categoryId =
                    intervention.care_category_id;
                const categoryName =
                    categoryMap[categoryId]
                        ?.name || "OTHER";

                if (!acc[categoryName]) {
                    acc[categoryName] = [];
                }
                acc[categoryName].push(
                    intervention,
                );
                return acc;
            },
            {} as Record<string, Intervention[]>,
        );

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
                        Care Interventions
                    </Text>
                </View>
            </Card.Header>
            <YStack p="$4" gap="$4">
                {Object.entries(
                    groupedInterventions,
                ).map(
                    ([
                        categoryName,
                        categoryInterventions,
                    ]) => (
                        <YStack
                            key={categoryName}
                            gap="$3"
                        >
                            {/* Category Header */}
                            <XStack
                                items="center"
                                gap="$2"
                                pb="$2"
                                borderBottomWidth={
                                    1
                                }
                                borderBottomColor="#e9ecef"
                            >
                                <Text
                                    fontSize="$5"
                                    fontWeight="600"
                                    color="#495057"
                                    textTransform="uppercase"
                                >
                                    {categoryName}
                                </Text>
                            </XStack>

                            {/* Interventions in this category */}
                            {categoryInterventions.map(
                                (
                                    intervention,
                                ) => (
                                    <Card
                                        key={
                                            intervention.wcp_intervention_id
                                        }
                                        bg="white"
                                        p="$3"
                                        bordered
                                    >
                                        <XStack
                                            items="center"
                                            gap="$3"
                                        >
                                            {/* Check icon */}
                                            <CheckCircle
                                                size={
                                                    16
                                                }
                                                color="#28a745"
                                            />

                                            {/* Intervention text */}
                                            <Text
                                                flex={
                                                    1
                                                }
                                                fontSize="$4"
                                                color="#495057"
                                            >
                                                {intervention.intervention_description ||
                                                    `${categoryName.toLowerCase()} intervention`}
                                            </Text>

                                            {/* Duration badge */}
                                            <Badge variant="success">
                                                <Text
                                                    fontSize="$3"
                                                    color="white"
                                                    fontWeight="500"
                                                >
                                                    {
                                                        intervention.duration_minutes
                                                    }{" "}
                                                    min
                                                </Text>
                                            </Badge>
                                        </XStack>
                                    </Card>
                                ),
                            )}
                        </YStack>
                    ),
                )}

                {/* Total Care Time */}
                <XStack
                    items="center"
                    justify="flex-end"
                    pt="$4"
                    mt="$4"
                    borderTopWidth={1}
                    borderTopColor="#e9ecef"
                    gap="$2"
                >
                    <Clock3
                        size={16}
                        color="#007bff"
                    />
                    <Text
                        fontSize="$5"
                        fontWeight="600"
                        color="#007bff"
                    >
                        Total Care Time:{" "}
                        {totalMinutes.toFixed(2)}{" "}
                        minutes
                    </Text>
                </XStack>
            </YStack>
        </Card>
    );
}
