import { useRouter } from "expo-router";
import React, {
    useCallback,
    useMemo,
} from "react";
import { showToastable } from "react-native-toastable";
import { YStack } from "tamagui";
import { z } from "zod";

import FlatList from "~/components/FlatList";
import LoadingScreen from "~/components/loaders/LoadingScreen";
import { useCarePlans } from "~/features/portal/care-plan/hook";
import { portalCarePlanListSchema } from "~/features/portal/care-plan/schema";

import {
    CarePlanCard,
    EmptyState,
    ErrorState,
    PullToRefresh,
    SearchInput,
    StatusFilter,
} from "./components";
import { carePlanListStore } from "./store";

type ICarePlan = z.infer<
    typeof portalCarePlanListSchema
>;

const CarePlanList = () => {
    const {
        data,
        isLoading,
        error,
        refetch,
        isFetching,
    } = useCarePlans();

    const {
        searchTerm,
        selectedStatus,
        setSearchTerm,
        setSelectedStatus,
    } = carePlanListStore();

    const router = useRouter();

    // Filter data based on search and status
    const filteredData = useMemo(() => {
        if (!data?.data) return [];

        let filtered = data.data;

        // Apply status filter
        if (selectedStatus !== "all") {
            filtered = filtered.filter(
                (carePlan: ICarePlan) =>
                    carePlan.status.toLowerCase() ===
                    selectedStatus.toLowerCase(),
            );
        }

        // Apply search filter
        if (searchTerm.trim()) {
            const searchLower =
                searchTerm.toLowerCase();
            filtered = filtered.filter(
                (carePlan: ICarePlan) =>
                    carePlan.author_name
                        .toLowerCase()
                        .includes(searchLower) ||
                    carePlan.date.includes(
                        searchTerm,
                    ) ||
                    carePlan.id
                        .toString()
                        .includes(searchTerm),
            );
        }

        return filtered;
    }, [data?.data, selectedStatus, searchTerm]);

    const handleRefresh = useCallback(() => {
        refetch();
    }, [refetch]);

    const handleStatusChange = useCallback(
        (status: string) => {
            setSelectedStatus(status);
        },
        [setSelectedStatus],
    );

    const handleSearch = useCallback(
        (term: string) => {
            setSearchTerm(term);
        },
        [setSearchTerm],
    );

    const handleClearSearch = useCallback(() => {
        setSearchTerm("");
    }, [setSearchTerm]);

    const handleView = useCallback(
        (id: number) => {
            router.push(
                `/(tabs)/(portal)/care-plan/${id}`,
            );
        },
        [router],
    );

    const handleAcknowledge = useCallback(
        (id: number) => {
            // TODO: Implement acknowledge functionality
            console.log(
                "Acknowledge care plan:",
                id,
            );
            showToastable({
                message: `Care plan #${id} acknowledged successfully`,
                status: "success",
            });
        },
        [],
    );

    const getEmptyStateMessage = () => {
        if (searchTerm.trim()) {
            return {
                hasSearch: true,
                searchTerm,
            };
        }

        if (selectedStatus !== "all") {
            return {
                hasSearch: false,
                searchTerm: "",
            };
        }

        return {
            hasSearch: false,
            searchTerm: "",
        };
    };

    if (isLoading && !data) {
        return <LoadingScreen />;
    }

    if (error) {
        return (
            <ErrorState
                onRetry={handleRefresh}
                message="Unable to load care plans. Please check your connection and try again."
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
                    {...getEmptyStateMessage()}
                />
            </PullToRefresh>
        );
    }

    return (
        <YStack flex={1} bg="$background">
            <YStack gap="$3" p="$4" pb="$2">
                <SearchInput
                    value={searchTerm}
                    onSearch={handleSearch}
                    placeholder="Search by author, date, or ID..."
                />
                <StatusFilter
                    selectedStatus={
                        selectedStatus
                    }
                    onStatusChange={
                        handleStatusChange
                    }
                    data={data.data}
                />
            </YStack>

            <YStack flex={1}>
                {filteredData.length === 0 ? (
                    <EmptyState
                        {...getEmptyStateMessage()}
                        onClearSearch={
                            searchTerm.trim()
                                ? handleClearSearch
                                : undefined
                        }
                    />
                ) : (
                    <FlatList<ICarePlan>
                        data={filteredData}
                        renderItem={({
                            item,
                        }) => (
                            <CarePlanCard
                                carePlan={item}
                                onView={
                                    handleView
                                }
                                onAcknowledge={
                                    handleAcknowledge
                                }
                            />
                        )}
                        keyExtractor={(item) =>
                            item.id.toString()
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
                        estimatedItemSize={180}
                    />
                )}
            </YStack>
        </YStack>
    );
};

export default CarePlanList;
