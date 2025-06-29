import * as Notifications from "expo-notifications";

export async function registerForPushNotification() {
    const { status: existingStatus } =
        await Notifications.getPermissionsAsync();
    let finalStatus = existingStatus;

    if (existingStatus !== "granted") {
        const { status } =
            await Notifications.requestPermissionsAsync();
        finalStatus = status;
    }

    if (finalStatus !== "granted") {
        alert(
            "Failed to get push token for push notification!",
        );
        return;
    }

    const tokenData =
        await Notifications.getExpoPushTokenAsync();
    return tokenData.data;
}

export const notificationHandler = () => {
    Notifications.setNotificationHandler({
        handleNotification: async () => {
            return {
                shouldPlaySound: true,
                shouldSetBadge: true,
                shouldShowAlert: true,
                shouldShowBanner: true,
                shouldShowList: true,
            };
        },
    });

    // Listener for received notifications
    Notifications.addNotificationReceivedListener(
        (notification) => {
            console.log(notification);
        },
    );

    // Listener for user interaction
    Notifications.addNotificationResponseReceivedListener(
        (response) => {
            console.log(response);
        },
    );
};
