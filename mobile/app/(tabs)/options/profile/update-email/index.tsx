import { Stack } from "expo-router"
import { SafeAreaView } from "react-native"

import UpdateEmail from "~/features/user/components/UpdateEmail"


const Screen = () => {
    return (
        <SafeAreaView style={style.container}>
            <Stack.Screen
                options={{
                    headerTitle: "Update Email",
                    headerShown: true,
                }}
            />
            <UpdateEmail />
        </SafeAreaView>
    )
}

const style = {
    container: {
        flex: 1,
    },
}

export default Screen;