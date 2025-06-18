import FlatList from "components/FlatList";
import RefreshButton from "features/portal/emergency-service/_components/refresh";
import { useEmergencyServiceRequestsHistory } from "features/portal/emergency-service/hook";
import { useEffect } from "react";
import { StyleSheet } from "react-native";
import {
    Card,
    H5,
    Spinner,
    Text,
    XStack,
    YStack,
} from "tamagui";

import RequestCard from "./RequestCard";
import type { EmergencyRequestHistory } from "./types";

const RequestHistory = () => {
    const {
        data: requests,
        isLoading,
        error,
        refetch: refetchRequests,
        isRefetching,
        reload
    } = useEmergencyServiceRequestsHistory();

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
                    <H5>Request History</H5>
                </Card.Header>
                <YStack
                    style={styles.centerContainer}
                >
                    <Spinner size="large" />
                    <Text
                        style={styles.loadingText}
                    >
                        Loading history...
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
                        Request History
                    </H5>
                </Card.Header>
                <YStack
                    style={styles.centerContainer}
                >
                    <Text
                        style={styles.errorText}
                    >
                        Error loading request
                        history
                    </Text>
                </YStack>
            </Card>
        );
    }

    const historyRequests = (requests ||
        []) as EmergencyRequestHistory[];

    const renderItem = ({
        item,
    }: {
        item: EmergencyRequestHistory;
    }) => <RequestCard request={item} />;

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
                <XStack
                    style={{
                        justifyContent:
                            "space-between",
                        alignItems: "center",
                    }}
                >
                    <H5 color="$white1">
                        Request History
                    </H5>
                    {isRefetching ? (
                        <Spinner size="small" />
                    ) : (
                        <RefreshButton
                            onPress={reload}
                        />
                    )}
                    {/* <Button
                        size="$2"
                        variant="outlined"
                        borderColor="$white1"
                        color="$white1"
                        bg="transparent"
                        onPress={() => {
                            // Add filter functionality here
                            console.log(
                                "Filter pressed",
                            );
                        }}
                    >
                        Filter
                    </Button> */}
                </XStack>
            </Card.Header>

            {historyRequests.length === 0 ? (
                <YStack
                    style={styles.centerContainer}
                >
                    <Text
                        style={styles.emptyText}
                    >
                        No request history
                    </Text>
                </YStack>
            ) : (
                <FlatList
                    data={historyRequests}
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

export default RequestHistory;
