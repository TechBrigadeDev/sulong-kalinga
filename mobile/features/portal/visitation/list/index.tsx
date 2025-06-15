import FlatList from "components/FlatList";
import LoadingScreen from "components/loaders/LoadingScreen";
import { useGetVisitations } from "features/portal/visitation/hook";
import { IVisitation } from "features/portal/visitation/type";
import { YStack } from "tamagui";

import {
    EmptyState,
    ErrorState,
    VisitationCard,
} from "./components";

const VisitationList = () => {
    const { data, isLoading, error, refetch } =
        useGetVisitations();

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (error) {
        return (
            <ErrorState
                onRetry={() => refetch()}
                message="Unable to load your visitations. Please check your connection and try again."
            />
        );
    }

    if (
        !data ||
        !data.data ||
        data.data.length === 0
    ) {
        return <EmptyState />;
    }

    return (
        <YStack flex={1} bg="$background">
            <FlatList<IVisitation>
                data={data.data}
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
            />
        </YStack>
    );
};

export default VisitationList;
