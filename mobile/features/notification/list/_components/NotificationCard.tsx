import { useReadNotification } from "features/notification/hook";
import {
    Card,
    Circle,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { INotification } from "~/features/notification/interface";
import {
    getNotificationBorderColor,
    getNotificationIcon,
    getNotificationIconColor,
    getRelativeTime,
} from "~/features/notification/list/utils";

interface NotificationCardProps {
    item: INotification;
    onPress?: (
        notification: INotification,
    ) => void;
}

export const NotificationCard = ({
    item,
    onPress,
}: NotificationCardProps) => {
    const IconComponent = getNotificationIcon(
        item.message_title,
    );
    const iconColor = getNotificationIconColor(
        item.message_title,
        item.is_read,
    );
    const borderColor =
        getNotificationBorderColor(
            item.message_title,
            item.is_read,
        );
    const relativeTime = getRelativeTime(
        item.date_created,
    );

    const {
        mutate: markAsRead,
        isPending: isMarkingAsRead,
    } = useReadNotification();

    const handlePress = () => {
        if (item.is_read || isMarkingAsRead) {
            return;
        }

        if (onPress) {
            onPress(item);
        }
        markAsRead(
            item.notification_id.toString(),
        );
    };

    const CardContent = () => (
        <Card
            elevate={!item.is_read}
            m="$2"
            p="$4"
            style={{
                backgroundColor: item.is_read
                    ? "#f9fafb"
                    : "white",
                borderColor: borderColor,
                borderLeftWidth: !item.is_read
                    ? 4
                    : 0,
                borderRadius: 12,
                shadowColor: "#000",
                shadowOffset: {
                    width: 0,
                    height: item.is_read ? 1 : 2,
                },
                shadowOpacity: item.is_read
                    ? 0.05
                    : 0.1,
                shadowRadius: item.is_read
                    ? 2
                    : 4,
                elevation: item.is_read ? 1 : 3,
            }}
            borderWidth={1}
            pressStyle={{
                scale: 0.98,
            }}
            animation="quick"
            onPress={handlePress}
        >
            <XStack
                gap="$3"
                style={{
                    alignItems: "flex-start",
                }}
            >
                {/* Icon Circle */}
                <Circle
                    size={40}
                    bg={
                        item.is_read
                            ? "#f3f4f6"
                            : `${iconColor}15`
                    }
                    style={{
                        alignItems: "center",
                        justifyContent: "center",
                        flexShrink: 0,
                    }}
                >
                    <IconComponent
                        size={20}
                        color={iconColor}
                    />
                </Circle>

                {/* Content */}
                <YStack flex={1} gap="$2">
                    {/* Header with title and time */}
                    <XStack
                        gap="$2"
                        style={{
                            justifyContent:
                                "space-between",
                            alignItems:
                                "flex-start",
                        }}
                    >
                        <Text
                            fontSize="$5"
                            fontWeight={
                                item.is_read
                                    ? "500"
                                    : "600"
                            }
                            color={
                                item.is_read
                                    ? "#374151"
                                    : "#111827"
                            }
                            numberOfLines={2}
                            flex={1}
                        >
                            {item.message_title}
                        </Text>
                        <Text
                            fontSize="$2"
                            color="#6b7280"
                            style={{
                                flexShrink: 0,
                                marginLeft: 8,
                            }}
                        >
                            {relativeTime}
                        </Text>
                    </XStack>

                    {/* Message Content */}
                    <Text
                        fontSize="$4"
                        color={
                            item.is_read
                                ? "#6b7280"
                                : "#374151"
                        }
                        lineHeight="$1"
                    >
                        {item.message}
                    </Text>

                    {/* Unread indicator */}
                    {!item.is_read && (
                        <XStack
                            gap="$2"
                            mt="$1"
                            style={{
                                alignItems:
                                    "center",
                            }}
                        >
                            <Circle
                                size={8}
                                bg={iconColor}
                            />
                            <Text
                                fontSize="$2"
                                color={iconColor}
                                fontWeight="600"
                            >
                                New
                            </Text>
                        </XStack>
                    )}
                </YStack>
            </XStack>
        </Card>
    );

    return <CardContent />;
};
