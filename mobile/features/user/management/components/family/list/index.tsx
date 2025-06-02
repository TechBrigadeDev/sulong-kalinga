import { useRouter } from "expo-router";
import { useGetFamilyMembers } from "features/user/management/management.hook";
import { IFamilyMember } from "features/user/management/management.type";
import { Button, Card, Text, View } from "tamagui";
import { FlashList, FlashListProps, ListRenderItem } from "@shopify/flash-list";

import { familyListStore } from "./store";
import { RefreshControl } from "react-native";

const FamilyList = () => {
    const {
        search,
    } = familyListStore();

    const {
        data = [],
        isLoading,
        refetch
    } = useGetFamilyMembers({
        search
    });

    if (data.length === 0 && !isLoading) {
        return (
            <Text>No family members found</Text>
        )
    }

    return (
        <FlashList
            data={data}
            renderItem={FamilyMemberCard}
            contentContainerStyle={{ 
                padding: 8,
            }}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
        />
    )
}

const FamilyMemberCard: ListRenderItem<IFamilyMember> = ({
    item
}) => { 
    const router = useRouter();

    const {
        family_member_id,
        first_name,
        last_name,
        relation_to_beneficiary
    } = item;
    
    const onView = () => {
        router.push(`/(tabs)/options/user-management/family/${family_member_id}`);
    }

    const onEdit = () => {
        router.push(`/(tabs)/options/user-management/family/${family_member_id}/edit`);
    }

    return (
        <Card 
            theme="light_white" 
            marginBottom="$2"
            marginHorizontal="$2"
            elevate
            shadowOpacity={0.1}
            bordered
            padding="$3">
            <View>
                <Text fontSize="$6" fontWeight="500">{first_name} {last_name}</Text>
                <Text fontSize="$4" color="gray">{relation_to_beneficiary}</Text>
            </View>
            <View style={{ flexDirection: 'row', gap: 8, marginTop: 12 }}>
                <Button
                    size="$3"
                    theme="light"
                    borderColor="gray"
                    onPress={onView}
                    variant="outlined"
                >
                    View
                </Button>
                <Button
                    size="$3"
                    theme="light"
                    borderColor="gray"
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
