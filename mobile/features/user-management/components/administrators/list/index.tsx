import { useRouter } from "expo-router";
import { useGetAdministrators } from "features/user-management/management.hook";
import { adminSchema } from "features/user-management/schema/admin";
import { Eye } from "lucide-react-native";
import { RefreshControl } from "react-native";
import {
    Button,
    Card,
    Text,
    View,
} from "tamagui";
import { type z } from "zod";

import FlatList from "~/components/FlatList";

import { adminListStore } from "./store";

type IAdmin = z.infer<typeof adminSchema>;

interface AdminCardProps {
    item: IAdmin;
}

const AdminList = () => {
    const { search } = adminListStore();

    const {
        data = [],
        isLoading,
        refetch,
    } = useGetAdministrators({
        search,
    });

    if (data.length === 0 && !isLoading) {
        return (
            <Text>No administrators found</Text>
        );
    }

    return (
        <FlatList<IAdmin>
            data={data}
            tabbed
            renderItem={({ item }) => (
                <AdminCard item={item} />
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

const AdminCard = ({ item }: AdminCardProps) => {
    const router = useRouter();
    const { id, first_name, last_name, email } =
        item;

    const onView = () => {
        router.push(
            `/(tabs)/options/user-management/admins/${id}`,
        );
    };

    // const onEdit = () => {
    //     router.push(
    //         `/(tabs)/options/user-management/admins/${id}/edit`,
    //     );
    // };

    return (
        <Card
            theme="light"
            marginBottom="$2"
            padding="$3"
            bg="#F8F9FA"
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
            flexDirection="row"
            items="center"
            justify="space-between"
        >
            <View>
                <Text
                    fontSize="$6"
                    fontWeight="500"
                    color="#495057"
                >
                    {first_name} {last_name}
                </Text>
                <Text
                    select="text"
                    fontSize="$4"
                    color="gray"
                >
                    {email}
                </Text>
            </View>
            <View
                style={{
                    flexDirection: "row",
                    gap: 8,
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
                    <Eye size={16} />
                </Button>
                {/* <Button
                    size="$3"
                    bg="#E9ECEF"
                    color="#495057"
                    borderColor="#DEE2E6"
                    onPress={onEdit}
                    variant="outlined"
                >
                    Edit
                </Button> */}
            </View>
        </Card>
    );
};

export default AdminList;
