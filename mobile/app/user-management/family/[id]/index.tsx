import { Stack, useLocalSearchParams } from "expo-router";
import { useGetFamilyMember } from "~/features/user/management/management.hook";
import { Text, View } from "tamagui";
import FamilyMemberDetail from "~/features/user/management/components/family/detail";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const {
        data,
        isLoading,
    } = useGetFamilyMember(id as string);

    if (isLoading) {
        return (
            <View>
                <Text>Loading...</Text>
            </View>
        )
    }

    if (!data) {
        return (
            <View>
                <Text>No family member found</Text>
            </View>
        )
    }

    return (
        <>
            <Stack.Screen/>
            <FamilyMemberDetail familyMember={data}/>
        </>
    )
}

export default Screen;