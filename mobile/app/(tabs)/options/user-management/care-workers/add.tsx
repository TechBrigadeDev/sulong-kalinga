import { Stack } from "expo-router";
import CareWorkerForm from "features/user-management/components/care-workers/form";

const Screen = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    headerShown: true,
                    title: "Add Care Worker",
                }}
            />
            <CareWorkerForm />
        </>
    );
};

export default Screen;
