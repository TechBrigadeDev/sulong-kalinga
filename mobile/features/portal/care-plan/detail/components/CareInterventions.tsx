import type { Intervention } from "features/portal/care-plan/schema";
import {
    Folder,
    HeartHandshake,
    Tag,
} from "lucide-react-native";
import React from "react";
import {
    Card,
    Separator,
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
        paddingVertical="$3"
        borderBottomWidth={1}
        borderBottomColor="$borderColor"
    >
        <XStack
            items="flex-start"
            justify="space-between"
            gap="$2"
        >
            <Text
                fontSize="$4"
                color="$color"
                fontWeight="500"
                flex={1}
            >
                {intervention.description}
            </Text>
            {intervention.duration && (
                <Card
                    backgroundColor="$blue3"
                    borderRadius="$2"
                    paddingHorizontal="$2"
                    paddingVertical="$1"
                >
                    <Text
                        fontSize="$2"
                        color="$blue11"
                        fontWeight="600"
                    >
                        {intervention.duration}
                    </Text>
                </Card>
            )}
        </XStack>

        {intervention.category && (
            <XStack items="center" gap="$1">
                <Tag size={14} color="grey" />
                <Text fontSize="$3" color="grey">
                    {intervention.category}
                </Text>
            </XStack>
        )}
    </YStack>
);

const InterventionCategory: React.FC<{
    category: string;
    interventions: Intervention[];
}> = ({ category, interventions }) => (
    <YStack gap="$2">
        <XStack items="center" gap="$2">
            <Folder size={16} color="blue" />
            <Text
                fontSize="$4"
                fontWeight="600"
                color="$blue11"
            >
                {category}
            </Text>
            <Text
                fontSize="$2"
                color="grey"
                backgroundColor="$gray3"
                paddingHorizontal="$2"
                paddingVertical="$1"
                borderRadius="$1"
            >
                {interventions.length}
            </Text>
        </XStack>

        <YStack gap="$0">
            {interventions.map(
                (intervention, index) => (
                    <InterventionItem
                        key={index}
                        intervention={
                            intervention
                        }
                    />
                ),
            )}
        </YStack>
    </YStack>
);

const CareInterventions: React.FC<
    CareInterventionsProps
> = ({ interventions = [] }) => {
    // Group interventions by category
    const groupedInterventions =
        interventions.reduce(
            (groups, intervention) => {
                const category =
                    intervention.category ||
                    "Other";
                if (!groups[category]) {
                    groups[category] = [];
                }
                groups[category].push(
                    intervention,
                );
                return groups;
            },
            {} as Record<string, Intervention[]>,
        );

    const categories = Object.keys(
        groupedInterventions,
    ).sort();

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
                            color="$purple10"
                        />
                        <Text
                            fontSize="$5"
                            fontWeight="600"
                            color="$color"
                        >
                            Care Interventions
                        </Text>
                    </XStack>
                    {interventions.length > 0 && (
                        <Card
                            bg="purple"
                            borderRadius="$2"
                            paddingHorizontal="$2"
                            paddingVertical="$1"
                        >
                            <Text
                                fontSize="$2"
                                color="purple"
                                fontWeight="600"
                            >
                                {
                                    interventions.length
                                }{" "}
                                total
                            </Text>
                        </Card>
                    )}
                </XStack>

                {categories.length > 0 ? (
                    <YStack gap="$4">
                        {categories.map(
                            (category, index) => (
                                <React.Fragment
                                    key={category}
                                >
                                    <InterventionCategory
                                        category={
                                            category
                                        }
                                        interventions={
                                            groupedInterventions[
                                                category
                                            ]
                                        }
                                    />
                                    {index <
                                        categories.length -
                                            1 && (
                                        <Separator borderColor="$borderColor" />
                                    )}
                                </React.Fragment>
                            ),
                        )}
                    </YStack>
                ) : (
                    <Text
                        fontSize="$4"
                        color="grey"
                        fontStyle="italic"
                        text="center"
                        paddingVertical="$2"
                    >
                        No care interventions
                        available
                    </Text>
                )}
            </YStack>
        </Card>
    );
};

export default CareInterventions;
