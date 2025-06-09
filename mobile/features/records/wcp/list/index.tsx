import { formatDate } from "common/date";
import Badge from "components/Bagde";
import FlatList from "components/FlatList";
import { useRouter } from "expo-router";
import {
    Eye,
    SquarePen,
} from "lucide-react-native";
import { RefreshControl } from "react-native";
import {
    Button,
    Card,
    H5,
    Spinner,
    Text,
    View,
    XStack,
    YStack,
} from "tamagui";

import { useWCPRecords } from "~/features/records/hook";
import { IWCPRecord } from "~/features/records/type";

import { wcpRecordsListStore } from "./store";

const WCPRecordsList = () => {
    const { search } = wcpRecordsListStore();

    const {
        data,
        isLoading,
        isFetchingNextPage,
        hasNextPage,
        fetchNextPage,
        refetch,
    } = useWCPRecords({
        search,
        limit: 10,
    });

    if (!data?.pages && isLoading) {
        return (
            <YStack
                flex={1}
                style={{
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <Spinner size="large" />
            </YStack>
        );
    }

    const allRecords =
        data?.pages.flatMap(
            (page) => page.data,
        ) || [];

    if (allRecords.length === 0 && !isLoading) {
        return (
            <YStack
                flex={1}
                style={{
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <Text>No WCP records found</Text>
            </YStack>
        );
    }

    const onLoadMore = () => {
        if (hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
        }
    };

    return (
        <FlatList
            data={allRecords}
            tabbed
            renderItem={({ item }) => (
                <WCPRecordCard record={item} />
            )}
            keyExtractor={(item, index) =>
                `${item.id}-${index}`
            }
            contentContainerStyle={{
                padding: 16,
            }}
            onEndReached={onLoadMore}
            onEndReachedThreshold={0.5}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
            ListFooterComponent={
                isFetchingNextPage ? (
                    <YStack
                        style={{
                            padding: 16,
                            alignItems: "center",
                        }}
                    >
                        <Spinner />
                    </YStack>
                ) : null
            }
            ListEmptyComponent={
                <View>
                    <Text>
                        No WCP records found.
                    </Text>
                </View>
            }
        />
    );
};

const WCPRecordCard = ({
    record,
}: {
    record: IWCPRecord;
}) => {
    const router = useRouter();
    const recordDate = formatDate(
        record.date,
        "MMM dd, yyyy",
    );

    const onViewDetails = () => {
        router.push(
            `/options/reports/care-records/${record.id}`,
        );
    };

    return (
        <Card
            theme="light"
            marginBottom="$2"
            padding="$3"
            bg="#F8F9FA"
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <YStack
                display="flex"
                flexDirection="row"
                justify="space-between"
                items="center"
                borderBlockEndColor="$accentColor"
                borderBlockEndWidth="$0.5"
                paddingBlock="$2"
            >
                <YStack>
                    <H5>
                        WCP Record #{record.id}
                    </H5>
                    <Badge borderRadius={4}>
                        Weekly Care Plan
                    </Badge>
                </YStack>
                <XStack>
                    <Button
                        size="$3"
                        bg="#E9ECEF"
                        color="#495057"
                        borderColor="#DEE2E6"
                        variant="outlined"
                        onPressIn={onViewDetails}
                    >
                        <Eye size={16} />
                    </Button>
                    <Button
                        size="$3"
                        bg="#E9ECEF"
                        color="#495057"
                        borderColor="#DEE2E6"
                        variant="outlined"
                    >
                        <SquarePen size={16} />
                    </Button>
                </XStack>
            </YStack>
            <YStack>
                <XStack
                    display="flex"
                    flexDirection="row"
                    justify="space-between"
                    paddingBlock="$2"
                >
                    <Text>Date</Text>
                    <Text>{recordDate}</Text>
                </XStack>
                {record.assessment && (
                    <XStack
                        display="flex"
                        flexDirection="column"
                        paddingBlock="$2"
                    >
                        <Text fontWeight="bold">
                            Assessment
                        </Text>
                        <Text
                            numberOfLines={3}
                            ellipsizeMode="tail"
                        >
                            {record.assessment}
                        </Text>
                    </XStack>
                )}
            </YStack>
        </Card>
    );
};

export default WCPRecordsList;
