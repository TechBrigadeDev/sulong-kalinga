import { Stack } from "expo-router";
import { useUserProfile } from "features/user/user.hook";
import {
    SafeAreaView,
    StyleSheet,
} from "react-native";

import ProfileSettings from "~/components/screens/Options/profile/Settings";

const ProfileScreen = () => {
    useUserProfile();
    return (
        <SafeAreaView style={style.container}>
            <Stack.Screen
                options={{
                    headerTitle: "Profile",
                    headerShown: true,
                }}
            />
            <ProfileSettings />
        </SafeAreaView>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
    },
});

export default ProfileScreen;
