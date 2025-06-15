import FlatList from "components/FlatList";
import { useRouter } from "expo-router";
import { useGetMedications } from "features/portal/medication/hook";
import EmptyState from "features/portal/medication/list/_components/EmptyState";
import ErrorState from "features/portal/medication/list/_components/ErrorState";
import { MedicationCard } from "features/portal/medication/list/_components/MedicationCard";
import { portalMedicationListStore } from "features/portal/medication/list/store";
import { MedicationSchedule } from "features/portal/medication/list/types";
import { useMemo } from "react";
import { RefreshControl } from "react-native";

const PortalMedicationList = () => {
    const { search, setSearch } =
        portalMedicationListStore();
    const { data, isLoading, isError, refetch } =
        useGetMedications();
    const router = useRouter();

    // Filter medications based on search
    const filteredMedications = useMemo(() => {
        if (!data || !Array.isArray(data))
            return [];

        if (!search.trim()) return data;

        return data.filter(
            (med: MedicationSchedule) => {
                // Add null checks for each property
                const medicationName =
                    med?.medication_name?.toLowerCase() ||
                    "";
                const medicationType =
                    med?.medication_type?.toLowerCase() ||
                    "";
                const dosage =
                    med?.dosage?.toLowerCase() ||
                    "";
                const searchTerm =
                    search.toLowerCase();

                return (
                    medicationName.includes(
                        searchTerm,
                    ) ||
                    medicationType.includes(
                        searchTerm,
                    ) ||
                    dosage.includes(searchTerm)
                );
            },
        );
    }, [data, search]);

    const handleViewDetails = (
        medicationId: number,
    ) => {
        router.push(
            `/(tabs)/(portal)/medication/${medicationId}`,
        );
    };

    const handleClearSearch = () => {
        setSearch("");
    };

    // Error state
    if (isError) {
        return <ErrorState onRetry={refetch} />;
    }

    // Empty state
    if (!data || data.length === 0) {
        return <EmptyState hasSearch={false} />;
    }

    // No search results
    if (
        filteredMedications.length === 0 &&
        search.trim()
    ) {
        return (
            <EmptyState
                hasSearch={true}
                onClearSearch={handleClearSearch}
            />
        );
    }

    return (
        <FlatList
            data={filteredMedications}
            renderItem={({ item }) => (
                <MedicationCard
                    item={item}
                    onViewDetails={
                        handleViewDetails
                    }
                />
            )}
            estimatedItemSize={220}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                    tintColor="#0066cc"
                    colors={["#0066cc"]}
                />
            }
            contentContainerStyle={{
                paddingBottom: 100,
            }}
            showsVerticalScrollIndicator={false}
        />
    );
};

export default PortalMedicationList;
