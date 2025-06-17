import Touchable from "components/Touchable";
import { useRouter } from "expo-router";
import { useNotifications } from "features/notification/hook";
import { Bell } from "lucide-react-native";
import { useCallback, useMemo } from "react";
import { SvgProps } from "react-native-svg";
import {
    Circle,
    Text,
    View,
    ViewProps,
} from "tamagui";

interface Props extends ViewProps {
    color: SvgProps["color"];
    onPress?: () => void | Promise<void>;
}

const NotificationButton = ({
    color = "white",
    rounded = "$radius.true",
    ...props
}: Props) => {
    const router = useRouter();
    const { data, isLoading } =
        useNotifications();

    const notifications = useMemo(() => {
        return (
            data?.pages
                .filter(
                    (page) =>
                        page.data.length > 0,
                )
                .map((page) =>
                    page.data.filter(
                        (notification) =>
                            !notification.is_read,
                    ),
                )
                .reduce(
                    (acc, page) =>
                        acc + page.length,
                    0,
                ) || 0
        );
    }, [data]);

    const Count = useCallback(() => {
        if (notifications > 0 && !isLoading) {
            return (
                <Circle
                    bg="red"
                    size={25}
                    position="absolute"
                    t={-10}
                    r={-10}
                    content="center"
                    items="center"
                    p={"$1.5"}
                    shadowColor={"black"}
                    shadowOffset={{
                        width: 0,
                        height: 2,
                    }}
                    shadowOpacity={0.3}
                    shadowRadius={3.5}
                    elevate
                >
                    <Text
                        color="white"
                        fontSize={10}
                        fontWeight="bold"
                    >
                        {notifications > 99
                            ? "99+"
                            : notifications}
                    </Text>
                </Circle>
            );
        }
        return null;
    }, [notifications, isLoading]);

    const onPress = async () => {
        if (props.onPress) {
            await props.onPress();
        }

        router.push("/notifications");
    };

    return (
        <View rounded={rounded} {...props}>
            <Touchable onPress={onPress}>
                <Bell
                    size={32}
                    color={color}
                    accessibilityLabel="Notifications"
                />
                <Count />
            </Touchable>
        </View>
    );
};

export default NotificationButton;
