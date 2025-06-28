import { useRouter } from "expo-router";
import { useGetFamilyMembers } from "features/user-management/management.hook";
import { IFamilyMember } from "features/user-management/management.type";
import { RefreshControl } from "react-native";
import {
    Button,
    Card,
    Text,
    View,
} from "tamagui";

import FlatList from "~/components/FlatList";

import { familyListStore } from "./store";

const FamilyList = () => {
    const { search } = familyListStore();

    const {
        data = [],
        isLoading,
        refetch,
    } = useGetFamilyMembers({
        search,
    });

    if (data.length === 0 && !isLoading) {
        return (
            <Text>No family members found</Text>
        );
    }

    return (
        <FlatList
            data={data}
            tabbed
            renderItem={({ item }) => (
                <FamilyMemberCard item={item} />
            )}
            contentContainerStyle={{
                paddingBottom: 120,
            }}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
        />
    );
};

interface FamilyMemberCardProps {
    item: IFamilyMember;
}

const FamilyMemberCard = ({
    item,
}: FamilyMemberCardProps) => {
    const router = useRouter();

    const {
        family_member_id,
        first_name,
        last_name,
        relation_to_beneficiary,
    } = item;

    const onView = () => {
        router.push(
            `/(tabs)/options/user-management/family/${family_member_id}`,
        );
    };

    const onEdit = () => {
        router.push(
            `/(tabs)/options/user-management/family/${family_member_id}/edit`,
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
            <View>
                <Text
                    fontSize="$6"
                    fontWeight="500"
                    color="#495057"
                >
                    {first_name} {last_name}
                </Text>
                <Text fontSize="$4" color="gray">
                    {relation_to_beneficiary}
                </Text>
            </View>
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

export default FamilyList;
