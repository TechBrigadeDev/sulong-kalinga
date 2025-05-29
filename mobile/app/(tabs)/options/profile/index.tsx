import { Stack } from "expo-router";
import { SafeAreaView, StyleSheet } from "react-native";
import ProfileSettings from "~/components/screens/Options/profile/Settings";

const ProfileScreen = () => {
    return (
        <SafeAreaView style={style.container}>
            <Stack.Screen 
                options={{ 
                    headerTitle: "Profile Settings",
                    headerShown: true,
                }} 
            />

            <ProfileSettings />
        </SafeAreaView>
    )
}

const style = StyleSheet.create({
    container: {
        flex: 1,
    }
});

export default ProfileScreen;