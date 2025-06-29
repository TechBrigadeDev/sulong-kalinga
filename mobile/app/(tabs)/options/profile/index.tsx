import LoadingScreen from "components/loaders/LoadingScreen";
import { Stack } from "expo-router";
import { useUserProfile } from "features/user/user.hook";
import {
    SafeAreaView,
    StyleSheet,
} from "react-native";

import ProfileSettings from "~/components/screens/Options/profile/Settings";

const ProfileScreen = () => {
    const { isLoading } = useUserProfile();

    const Screen = () =>
        isLoading ? (
            <LoadingScreen />
        ) : (
            <ProfileSettings />
        );

    return (
        <SafeAreaView style={style.container}>
            <Stack.Screen
                options={{
                    headerTitle: "Profile",
                    headerShown: true,
                }}
            />
            <Screen />
        </SafeAreaView>
    );
};

const style = StyleSheet.create({
    container: {
        flex: 1,
    },
});

export default ProfileScreen;
