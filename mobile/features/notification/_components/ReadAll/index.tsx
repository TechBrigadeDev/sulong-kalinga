import { useReadAllNotifications } from "features/notification/hook";
import { Button, Spinner } from "tamagui";

const ReadAllNotification = () => {
    const { mutate, isPending } =
        useReadAllNotifications();

    const onMarkAllAsRead = () => {
        mutate();
    };

    const disabled = isPending;

    return (
        <Button
            size="$3"
            theme="blue"
            fontSize="$2"
            disabled={disabled}
            onPress={onMarkAllAsRead}
            maxW={120}
        >
            {isPending && (
                <Spinner size="small" />
            )}
            Mark all as read
        </Button>
    );
};

export default ReadAllNotification;
