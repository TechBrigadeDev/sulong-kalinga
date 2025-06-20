import FlatList from "components/FlatList";
import RefreshButton from "features/portal/emergency-service/_components/refresh";
import { useEmergencyServiceRequests } from "features/portal/emergency-service/hook";
import { RefObject, useEffect } from "react";
import { StyleSheet } from "react-native";
import {
    Card,
    H5,
    Spinner,
    TamaguiElement,
    Text,
    YStack,
} from "tamagui";

import RequestCard from "./RequestCard";
import type { EmergencyRequest } from "./types";

const Cards = ({
    onEdit,
}: {
    onEdit: () => void;
}) => {
    const {
        data: requests,
        isLoading,
        error,
        refetch: refetchRequests,
        isRefetching,
    } = useEmergencyServiceRequests();

    useEffect(() => {
        refetchRequests();
    }, [refetchRequests]);

    if (isLoading) {
        return (
            <Card
                marginBottom="$2"
                borderRadius={8}
                borderWidth={1}
                elevate
                overflow="hidden"
            >
                <Card.Header
                    padded
                    paddingBlock="$2"
                    bg="#2d3748"
                >
                    <H5 color="$white1">
                        Active Requests
                    </H5>
                </Card.Header>
                <YStack
                    style={styles.centerContainer}
                >
                    <Spinner size="large" />
                    <Text
                        style={styles.loadingText}
                    >
                        Loading requests...
                    </Text>
                </YStack>
            </Card>
        );
    }

    if (error) {
        return (
            <Card
                marginBottom="$2"
                borderRadius={8}
                borderWidth={1}
                elevate
                overflow="hidden"
            >
                <Card.Header
                    padded
                    paddingBlock="$2"
                    bg="#2d3748"
                >
                    <H5 color="$white1">
                        Active Emergency Requests
                    </H5>
                </Card.Header>
                <YStack
                    style={styles.centerContainer}
                >
                    <Text
                        style={styles.errorText}
                    >
                        Error loading requests
                    </Text>
                </YStack>
            </Card>
        );
    }

    const activeRequests = requests || [];

    const renderItem = ({
        item,
    }: {
        item: EmergencyRequest;
    }) => (
        <RequestCard
            request={item}
            onEdit={onEdit}
        />
    );

    return (
        <Card
            marginBottom="$2"
            borderRadius={8}
            borderWidth={1}
            elevate
            overflow="hidden"
        >
            <Card.Header
                padded
                paddingBlock="$2"
                bg="#2d3748"
                flexDirection="row"
                justify="space-between"
                items="center"
            >
                <H5 color="$white1">
                    Active Requests
                </H5>
                {isRefetching ? (
                    <Spinner size="small" />
                ) : (
                    <RefreshButton
                        onPress={refetchRequests}
                    />
                )}
            </Card.Header>

            {activeRequests.length === 0 ? (
                <YStack
                    style={styles.centerContainer}
                >
                    <Text
                        style={styles.emptyText}
                    >
                        No active requests
                    </Text>
                </YStack>
            ) : (
                <FlatList
                    data={activeRequests}
                    renderItem={renderItem}
                    estimatedItemSize={200}
                    contentContainerStyle={{
                        paddingHorizontal: 16,
                    }}
                    keyExtractor={(item) =>
                        item.id.toString()
                    }
                />
            )}
        </Card>
    );
};

const styles = StyleSheet.create({
    centerContainer: {
        padding: 16,
        alignItems: "center",
        justifyContent: "center",
    },
    loadingText: {
        marginTop: 8,
    },
    errorText: {
        color: "#ef4444",
    },
    emptyText: {
        opacity: 0.6,
    },
});

const ActiveRequests = ({
    ref,
    onEdit,
}: {
    ref?: RefObject<TamaguiElement>;
    onEdit: () => void;
}) => {
    return (
        <YStack ref={ref}>
            <Cards onEdit={onEdit} />
        </YStack>
    );
};

export default ActiveRequests;
