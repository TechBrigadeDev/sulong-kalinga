import { portalCarePlanListSchema } from "features/portal/care-plan/schema";
import { useMemo } from "react";
import {
    Button,
    ScrollView,
    Text,
    XStack,
} from "tamagui";
import { z } from "zod";

type ICarePlan = z.infer<
    typeof portalCarePlanListSchema
>;

interface Props {
    selectedStatus: string;
    onStatusChange: (status: string) => void;
    data: ICarePlan[];
}

const StatusFilter = ({
    selectedStatus,
    onStatusChange,
    data,
}: Props) => {
    const statusCounts = useMemo(() => {
        const counts = {
            all: data.length,
            "pending review": 0,
            acknowledged: 0,
            completed: 0,
        };

        data.forEach((carePlan) => {
            const status =
                carePlan.status.toLowerCase();
            if (status === "pending review") {
                counts["pending review"]++;
            } else if (
                status === "acknowledged"
            ) {
                counts.acknowledged++;
            } else if (status === "completed") {
                counts.completed++;
            }
        });

        return counts;
    }, [data]);

    const filters = [
        {
            key: "all",
            label: "All",
            count: statusCounts.all,
        },
        {
            key: "pending review",
            label: "Pending Review",
            count: statusCounts["pending review"],
        },
        {
            key: "acknowledged",
            label: "Acknowledged",
            count: statusCounts.acknowledged,
        },
        {
            key: "completed",
            label: "Completed",
            count: statusCounts.completed,
        },
    ];

    return (
        <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={{
                paddingInline: 16,
                gap: 8,
            }}
            style={{ flexGrow: 0 }}
        >
            <XStack gap="$2">
                {filters.map((filter) => {
                    const isSelected =
                        selectedStatus ===
                        filter.key;

                    return (
                        <Button
                            key={filter.key}
                            size="$3"
                            variant="outlined"
                            theme={
                                isSelected
                                    ? "blue"
                                    : undefined
                            }
                            bg={
                                isSelected
                                    ? "$blue8"
                                    : "transparent"
                            }
                            borderColor={
                                isSelected
                                    ? "$blue8"
                                    : "$borderColor"
                            }
                            onPress={() =>
                                onStatusChange(
                                    filter.key,
                                )
                            }
                            disabled={
                                filter.count === 0
                            }
                            opacity={
                                filter.count === 0
                                    ? 0.5
                                    : 1
                            }
                        >
                            <Text
                                color={
                                    isSelected
                                        ? "$white1"
                                        : "$color"
                                }
                                fontSize="$3"
                                fontWeight="500"
                            >
                                {filter.label} (
                                {filter.count})
                            </Text>
                        </Button>
                    );
                })}
            </XStack>
        </ScrollView>
    );
};

export default StatusFilter;
