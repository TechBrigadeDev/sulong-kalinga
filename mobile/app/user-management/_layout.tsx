import { Stack, useRouter } from "expo-router";
import { Button } from "tamagui";

const screens = [
    {
        name: "beneficiaries",
        title: "Beneficiaries",
    },
    {
        name: "family",
        title: "Family or Relatives",
    },
    {
        name: "care-workers",
        title: "Care Workers",
    },
    {
        name: "care-managers",
        title: "Care Managers",
    },
    {
        name: "administrators",
        title: "Administrators",
    }
]

const Layout = () => {
    return (
        <Stack screenOptions={{
            headerShown: true,
            headerBackButtonMenuEnabled: true,
            headerBackVisible: true,
            
        }}>
            {screens.map((screen) => (
                <Stack.Screen 
                    key={screen.name} 
                    name={screen.name} 
                    options={{ title: screen.title }} 
                />
            ))}
        </Stack>
    )
}

export default Layout;