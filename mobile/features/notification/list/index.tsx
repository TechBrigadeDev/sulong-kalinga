import { useMemo } from "react";
import { RefreshControl } from "react-native";
import { Spinner, YStack } from "tamagui";

import FlatList from "~/components/FlatList";
import { useNotifications } from "~/features/notification/hook";
import { INotification } from "~/features/notification/interface";

import {
    EmptyState,
    NotificationCard,
} from "./_components";

const NotificationList = () => {
    const {
        data,
        isLoading,
        isFetchingNextPage,
        hasNextPage,
        fetchNextPage,
        refetch,
    } = useNotifications();

    // Flatten the pages data
    const notifications: INotification[] =
        useMemo(
            () =>
                data?.pages.flatMap(
                    (page) => page.data,
                ) || [],
            [data],
        );

    const handleLoadMore = () => {
        if (hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
        }
    };

    const handleNotificationPress = (
        notification: INotification,
    ) => {
        // TODO: Mark as read and navigate to detail view
        console.log(
            "Notification pressed:",
            notification,
        );
    };

    // Loading state
    if (isLoading && !data) {
        return (
            <YStack
                flex={1}
                style={{
                    alignItems: "center",
                    justifyContent: "center",
                }}
            >
                <Spinner size="large" />
            </YStack>
        );
    }

    // Empty state when no notifications
    if (!notifications.length) {
        return <EmptyState />;
    }

    return (
        <YStack flex={1}>
            <FlatList
                data={notifications}
                renderItem={({ item }) => (
                    <NotificationCard
                        item={item}
                        onPress={
                            handleNotificationPress
                        }
                    />
                )}
                onEndReached={handleLoadMore}
                onEndReachedThreshold={0.5}
                estimatedItemSize={120}
                refreshControl={
                    <RefreshControl
                        refreshing={isLoading}
                        onRefresh={refetch}
                        tintColor="#0066cc"
                        colors={["#0066cc"]}
                    />
                }
                ListFooterComponent={
                    isFetchingNextPage ? (
                        <YStack
                            style={{
                                padding: 16,
                                alignItems:
                                    "center",
                            }}
                        >
                            <Spinner />
                        </YStack>
                    ) : null
                }
                contentContainerStyle={{
                    paddingBottom: 100,
                }}
                showsVerticalScrollIndicator={
                    false
                }
            />
        </YStack>
    );
};

export default NotificationList;
