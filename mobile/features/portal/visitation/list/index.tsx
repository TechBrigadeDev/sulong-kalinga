import { isSameDay } from "common/date";
import FlatList from "components/FlatList";
import LoadingScreen from "components/loaders/LoadingScreen";
import { useGetVisitations } from "features/portal/visitation/hook";
import { IVisitation } from "features/portal/visitation/type";
import {
    useCallback,
    useMemo,
    useState,
} from "react";
import { YStack } from "tamagui";

import {
    EmptyState,
    ErrorState,
    PullToRefresh,
    StatusFilter,
    VisitationCard,
} from "./components";
import { portalVisitationListStore } from "./store";

const VisitationList = () => {
    const {
        data,
        isLoading,
        error,
        refetch,
        isFetching,
    } = useGetVisitations();

    const { currentDate } =
        portalVisitationListStore();

    const [selectedStatus, setSelectedStatus] =
        useState<IVisitation["status"] | "all">(
            "all",
        );

    const dateFilteredData = useMemo(() => {
        if (!data?.data) return [];

        // Filter by selected date only
        if (currentDate) {
            return data.data.filter(
                (visitation) =>
                    isSameDay(
                        visitation.occurrence_date,
                        currentDate,
                    ),
            );
        }

        return data.data;
    }, [data?.data, currentDate]);

    const filteredData = useMemo(() => {
        // Apply status filter to date-filtered data
        if (selectedStatus === "all") {
            return dateFilteredData;
        }

        return dateFilteredData.filter(
            (visitation) =>
                visitation.status ===
                selectedStatus,
        );
    }, [dateFilteredData, selectedStatus]);

    const handleRefresh = useCallback(() => {
        refetch();
    }, [refetch]);

    const handleStatusChange = useCallback(
        (
            status: IVisitation["status"] | "all",
        ) => {
            setSelectedStatus(status);
        },
        [],
    );

    // Generate contextual empty state message
    const getEmptyStateMessage = () => {
        const { currentDate } =
            portalVisitationListStore();
        const hasDateFilter =
            currentDate &&
            !isSameDay(currentDate, new Date());

        if (hasDateFilter) {
            return `No visitations scheduled for ${currentDate.toLocaleDateString()}. Try selecting a different date.`;
        }

        if (selectedStatus === "all") {
            return "You don't have any visitations scheduled at the moment.";
        }

        const statusLabels = {
            scheduled: "scheduled",
            completed: "completed",
            canceled: "canceled",
        };

        return `No ${statusLabels[selectedStatus]} visitations found. Try selecting a different status filter.`;
    };

    if (isLoading && !data) {
        return <LoadingScreen />;
    }

    if (error) {
        return (
            <ErrorState
                onRetry={handleRefresh}
                message="Unable to load your visitations. Please check your connection and try again."
            />
        );
    }

    if (
        !data ||
        !data.data ||
        data.data.length === 0
    ) {
        return (
            <PullToRefresh
                refreshing={isFetching}
                onRefresh={handleRefresh}
            >
                <EmptyState
                    message={getEmptyStateMessage()}
                />
            </PullToRefresh>
        );
    }

    return (
        <YStack flex={1} bg="$background">
            {/* <StatusFilter
                selectedStatus={selectedStatus}
                onStatusChange={
                    handleStatusChange
                }
                data={dateFilteredData || []}
            /> */}
            <YStack flex={1}>
                <FlatList<IVisitation>
                    data={filteredData || []}
                    renderItem={({ item }) => (
                        <VisitationCard
                            visitation={item}
                        />
                    )}
                    keyExtractor={(item) =>
                        `${item.visitation_id}-${item.occurrence_id}`
                    }
                    showsVerticalScrollIndicator={
                        false
                    }
                    contentContainerStyle={{
                        padding: 16,
                        paddingBottom: 32,
                    }}
                    refreshing={isFetching}
                    onRefresh={handleRefresh}
                    ListEmptyComponent={() => (
                        <EmptyState />
                    )}
                />
            </YStack>
        </YStack>
    );
};

export default VisitationList;
