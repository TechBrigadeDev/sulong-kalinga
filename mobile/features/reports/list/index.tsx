import { formatDate } from "common/date";
import Badge from "components/Bagde";
import FlatList from "components/FlatList";
import LoadingScreen from "components/loaders/LoadingScreen";
import { IReport } from "features/reports/type";
import {
    Eye,
    SquarePen,
} from "lucide-react-native";
import { useCallback } from "react";
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

import { useCarePlans } from "~/features/reports/hook";

import { reportsListStore } from "./store";

const ReportsList = () => {
    const { search } = reportsListStore();

    const {
        data,
        isLoading,
        isFetchingNextPage,
        hasNextPage,
        fetchNextPage,
        refetch,
    } = useCarePlans({
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

    const allReports = data?.pages.flatMap((page) => page.data) || [];

    if (allReports.length === 0 && !isLoading) {
        return (
            <YStack
                flex={1}
                style={{
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <Text>No reports found</Text>
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
            data={allReports}
            tabbed
            renderItem={({ item }) => (
                <ReportCard report={item} />
            )}
            keyExtractor={(item, index) =>
                `${item.beneficiary_id}-${index}`
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
                    <Text>No reports found.</Text>
                </View>
            }
        />
    );
};

const ReportCard = ({
    report,
}: {
    report: IReport;
}) => {
    const author = `${report.author_first_name} ${report.author_last_name}`;
    const beneficiary = `${report.beneficiary_first_name} ${report.beneficiary_last_name}`;
    const uploadedAt = formatDate(
        report.created_at,
        "MMM dd, yyyy",
    );

    const Status = useCallback(() => {
        return (
            <Badge borderRadius={4}>
                {report.report_type}
            </Badge>
        );
    }, [report.report_type]);

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
                    <H5>{author}</H5>
                    <Status />
                </YStack>
                <XStack>
                    <Button
                        size="$3"
                        bg="#E9ECEF"
                        color="#495057"
                        borderColor="#DEE2E6"
                        variant="outlined"
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
                    <Text>Beneficiary</Text>
                    <Text>{beneficiary}</Text>
                </XStack>
                <XStack
                    display="flex"
                    flexDirection="row"
                    justify="space-between"
                    paddingBlock="$2"
                >
                    <Text>Uploaded At</Text>
                    <Text>{uploadedAt}</Text>
                </XStack>
            </YStack>
        </Card>
    );
};

export default ReportsList;
