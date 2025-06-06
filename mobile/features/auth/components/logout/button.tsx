import { useLogout } from "features/auth/auth.hook";
import { Button } from "tamagui";

const LogoutButton = () => {
    const { logout } = useLogout();

    const onPress = async () => {
        await logout();
    };

    return (
        <Button onPress={onPress} size="$4">
            Logout
        </Button>
    );
};

export default LogoutButton;
