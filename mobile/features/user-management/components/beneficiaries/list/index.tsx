import { useRouter } from "expo-router";
import { useGetBeneficiaries } from "features/user-management/management.hook";
import { RefreshControl } from "react-native";
import {
    Button,
    Card,
    Spinner,
    Text,
    View,
    YStack,
} from "tamagui";

import FlatList from "~/components/FlatList";
import { IBeneficiary } from "~/features/user-management/management.type";

import { beneficiaryListStore } from "./store";

const BeneficiaryList = () => {
    const { search } = beneficiaryListStore();

    const {
        data,
        isLoading,
        isFetchingNextPage,
        hasNextPage,
        fetchNextPage,
        refetch,
    } = useGetBeneficiaries({
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

    if (
        data?.pages[0].data.length === 0 &&
        !isLoading
    ) {
        return (
            <YStack
                flex={1}
                style={{
                    justifyContent: "center",
                    alignItems: "center",
                }}
            >
                <Text>
                    No beneficiaries found
                </Text>
            </YStack>
        );
    }

    const onLoadMore = () => {
        if (hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
        }
    };

    const allBeneficiaries =
        data?.pages.flatMap(
            (page) => page.data,
        ) || [];

    return (
        <View style={{ flex: 1 }}>
            <FlatList
                data={allBeneficiaries}
                renderItem={({ item }) => (
                    <BeneficiaryCard
                        item={item}
                    />
                )}
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
                                alignItems:
                                    "center",
                            }}
                        >
                            <Spinner />
                        </YStack>
                    ) : null
                }
            />
        </View>
    );
};

interface BeneficiaryCardProps {
    item: IBeneficiary;
}

const BeneficiaryCard = ({
    item,
}: BeneficiaryCardProps) => {
    const router = useRouter();
    const {
        beneficiary_id,
        first_name,
        last_name,
    } = item;

    const onView = () => {
        router.push(
            `/(tabs)/options/user-management/beneficiaries/${beneficiary_id}`,
        );
    };

    const onEdit = () => {
        router.push(
            `/(tabs)/options/user-management/beneficiaries/${beneficiary_id}/edit`,
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
            <Text
                fontSize="$6"
                fontWeight="500"
                color="#495057"
            >
                {first_name} {last_name}
            </Text>
            <View
                style={{
                    flexDirection: "row",
                    gap: 8,
                    marginTop: 12,
                }}
            >
                <Button
                    size="$3"
                    bg="#E9ECEF"
                    color="#495057"
                    borderColor="#DEE2E6"
                    onPress={onView}
                    variant="outlined"
                >
                    View
                </Button>
                <Button
                    size="$3"
                    bg="#E9ECEF"
                    color="#495057"
                    borderColor="#DEE2E6"
                    onPress={onEdit}
                    variant="outlined"
                >
                    Edit
                </Button>
            </View>
        </Card>
    );
};

export default BeneficiaryList;
