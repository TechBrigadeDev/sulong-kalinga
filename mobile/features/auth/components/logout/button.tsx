import { Button } from "tamagui";
import { useLogout } from "../../auth.hook";


const LogoutButton = () => {
    const {
        logout
    } = useLogout();

    const onPress = async () => {
        await logout();
    }

    return (
        <Button
            onPress={onPress}
            size="$4">
            Logout
        </Button>
    )
}


export default LogoutButton;