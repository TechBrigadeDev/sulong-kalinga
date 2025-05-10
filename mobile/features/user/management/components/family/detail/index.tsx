import { SafeAreaView } from "react-native";
import { ScrollView, YStack } from "tamagui";
import { IFamilyMember } from "../../../../user.schema";
import { Stack } from "expo-router";
import FamilyMemberHeader from "./FamilyMemberHeader";
import PersonalInformation from "./PersonalInformation";

interface IDetailProps {
    familyMember: IFamilyMember;
}

const FamilyMemberDetail = ({
    familyMember
}: IDetailProps) => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen options={{
                title: "VIEW FAMILY PROFILE DETAILS",
                headerShown: true,
            }} />
            <ScrollView>
                <YStack space="$4" style={{ padding: 16 }}>
                    <FamilyMemberHeader familyMember={familyMember} />
                    <PersonalInformation familyMember={familyMember} />
                </YStack>
            </ScrollView>
        </SafeAreaView>
    )
}

export default FamilyMemberDetail;
