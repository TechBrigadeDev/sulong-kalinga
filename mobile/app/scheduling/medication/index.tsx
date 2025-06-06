import { WeekCalendar } from "components/Calendar";
import { Stack } from "expo-router";
import { View } from "tamagui";

const Screen = () => {
    return (
        <View flex={1} bg="$background">
            <WeekCalendar
                provider={{
                    date: new Date().toISOString().split("T")[0],
                }}
            />
        </View>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                title: "Medication Schedule",
                headerShown: true,
            }}
        />
        <Screen />
    </>
);

export default Layout;
