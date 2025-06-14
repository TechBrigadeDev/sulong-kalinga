import { useRouter } from "expo-router";
import { Button } from "tamagui";

export const ToSignature = () => {
    const router = useRouter();

    const handlePress = () => {
        router.push("/(modals)/signature");
    };
    return (
        <Button onPress={handlePress}>
            Go to Signature
        </Button>
    );
};
