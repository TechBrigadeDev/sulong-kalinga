import { Stack, useRouter, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";
import { useGetFamilyMember } from "../../../../../features/user/management/management.hook";
import FamilyMemberForm from "../../../../../features/user/management/components/family/FamilyMemberForm";
import { IFamilyMember } from "../../../../../features/user/user.schema";

const Screen = () => {
    const { id } = useLocalSearchParams();
    const router = useRouter();

    const {
        data,
        isLoading
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

    const handleSubmit = (formData: Partial<IFamilyMember>) => {
        // TODO: Add API call to update family member
        console.log("Updating family member:", formData);
        router.back();
    };

    return (
        <>
            <Stack.Screen
                options={{
                    title: 'Edit Family Member',
                }}
            />
            <FamilyMemberForm 
                familyMember={data} 
                onSubmit={handleSubmit}
            />
        </>
    )
}

export default Screen;
