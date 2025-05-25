import { FlatList, ListRenderItem, RefreshControl } from "react-native";
import { useGetFamilyMembers } from "../../../management.hook";
import { Button, Card, Text, View } from "tamagui";
import { familyListStore } from "./store";
import { useRouter } from "expo-router";
import FamilySearch from "./search";
import { IFamilyMember } from "~/features/user/user.schema";

const FamilyList = () => {
    const router = useRouter();
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

    const handleAddFamilyMember = () => {
        router.push("/(tabs)/options/user-management/family/add");
    };

    if (data.length === 0 && !isLoading) {
        return (
            <View style={{ flex: 1, padding: 8 }}>
                <Card
                    paddingVertical={20}
                    marginVertical={20}
                    borderRadius={10}
                    display="flex"
                    gap="$4"
                >
                    <Button
                        size="$3"
                        theme="dark_blue"
                        onPressIn={handleAddFamilyMember}
                    >
                        Add Family Member
                    </Button>
                    <FamilySearch/>
                </Card>
                <Text>No family members found</Text>
            </View>
        )
    }

    return (
        <View style={{ flex: 1 }}>
            <Card
                paddingVertical={20}
                marginVertical={20}
                borderRadius={10}
                display="flex"
                gap="$4"
                margin="$2"
            >
                <Button
                    size="$3"
                    theme="dark_blue"
                    onPressIn={handleAddFamilyMember}
                >
                    Add Family Member
                </Button>
                <FamilySearch/>
            </Card>
            <FlatList 
                data={data}
                renderItem={FamilyMemberCard}
                contentContainerStyle={{ padding: 8 }}
                refreshControl={
                    <RefreshControl
                        refreshing={isLoading}
                        onRefresh={refetch}
                    />
                }
            />
        </View>
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
