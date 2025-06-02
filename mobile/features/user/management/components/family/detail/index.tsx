import { Stack } from "expo-router";
import { IFamilyMember } from "features/user/management/management.type";
import { SafeAreaView, StyleSheet } from "react-native";
import { ScrollView, YStack } from "tamagui";

import FamilyMemberHeader from "./FamilyMemberHeader";
import PersonalInformation from "./PersonalInformation";
import TabScroll from "../../../../../../components/tabs/TabScroll";

interface IDetailProps {
    familyMember: IFamilyMember;
}

const FamilyMemberDetail = ({
    familyMember
}: IDetailProps) => {
    return (
        <SafeAreaView style={styles.container}>
            <Stack.Screen options={{
                title: "Family Member",
                headerShown: true,
            }} />
            <TabScroll>
                <YStack gap="$4" style={{ padding: 16 }}>
                    <FamilyMemberHeader familyMember={familyMember} />
                    <PersonalInformation familyMember={familyMember} />
                </YStack>
            </TabScroll>
        </SafeAreaView>
    )
}

const styles = StyleSheet.create({
})

export default FamilyMemberDetail;
