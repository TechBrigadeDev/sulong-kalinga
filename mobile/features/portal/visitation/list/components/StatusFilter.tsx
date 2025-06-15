import { IVisitation } from "features/portal/visitation/type";
import { useMemo } from "react";
import {
    Button,
    ScrollView,
    Text,
    XStack,
} from "tamagui";

interface Props {
    selectedStatus: IVisitation["status"] | "all";
    onStatusChange: (
        status: IVisitation["status"] | "all",
    ) => void;
    data: IVisitation[];
}

const StatusFilter = ({
    selectedStatus,
    onStatusChange,
    data,
}: Props) => {
    const statusCounts = useMemo(() => {
        const counts = {
            all: data.length,
            scheduled: 0,
            completed: 0,
            canceled: 0,
        };

        data.forEach((visitation) => {
            counts[visitation.status]++;
        });

        return counts;
    }, [data]);

    const filters = [
        {
            key: "all" as const,
            label: "All",
            count: statusCounts.all,
        },
        {
            key: "scheduled" as const,
            label: "Scheduled",
            count: statusCounts.scheduled,
        },
        {
            key: "completed" as const,
            label: "Completed",
            count: statusCounts.completed,
        },
        {
            key: "canceled" as const,
            label: "Canceled",
            count: statusCounts.canceled,
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
            flex={1}
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
