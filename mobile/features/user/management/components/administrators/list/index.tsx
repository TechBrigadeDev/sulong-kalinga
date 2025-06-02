import { FlashList, ListRenderItem } from "@shopify/flash-list";
import { useRouter } from "expo-router";
import { useGetAdministrators } from "features/user/management/management.hook";
import { adminSchema } from "features/user/management/schema/admin";
import { RefreshControl } from "react-native";
import { Button, Card, Text, View } from "tamagui";
import { type z } from "zod";

import { adminListStore } from "./store";

type IAdmin = z.infer<typeof adminSchema>;

const AdminList = () => {
    const {
        search,
    } = adminListStore();

    const {
        data = [],
        isLoading,
        refetch
    } = useGetAdministrators({
        search
    });

    if (data.length === 0 && !isLoading) {
        return (
            <Text>No administrators found</Text>
        )
    }

    return (
        <FlashList
            data={data}
            renderItem={AdminCard}
            contentContainerStyle={{ 
                padding: 8,
                paddingBottom: 100,
            }}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
            estimatedItemSize={100}
        />
    )
}

const AdminCard: ListRenderItem<IAdmin> = ({
    item
}) => { 
    const router = useRouter();

    const {
        id,
        first_name,
        last_name,
        email,
    } = item;
    
    const onView = () => {
        router.push(`/(tabs)/options/user-management/admins/${id}`);
    }

    const onEdit = () => {
        router.push(`/(tabs)/options/user-management/admins/${id}/edit`);
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
                <Text fontSize="$4" color="gray">{email}</Text>
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

export default AdminList;
