import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import { IFamilyMember } from "features/user-management/management.type";
import { SafeAreaView, StyleSheet } from "react-native";
import { YStack } from "tamagui";

import FamilyMemberHeader from "./FamilyMemberHeader";
import PersonalInformation from "./PersonalInformation";

interface IDetailProps {
    familyMember: IFamilyMember;
}

const FamilyMemberDetail = ({ familyMember }: IDetailProps) => {
    return (
        <SafeAreaView style={styles.container}>
            <Stack.Screen
                options={{
                    title: "Family Member",
                    headerShown: true,
                }}
            />
            <TabScroll>
                <YStack gap="$4" style={{ padding: 16 }}>
                    <FamilyMemberHeader familyMember={familyMember} />
                    <PersonalInformation familyMember={familyMember} />
                </YStack>
            </TabScroll>
        </SafeAreaView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
});

export default FamilyMemberDetail;
