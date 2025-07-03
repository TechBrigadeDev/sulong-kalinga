import { PropsWithChildren } from "react";

import { useRegisterNotification } from "./hook";

const NotificationProvider = ({
    children,
}: PropsWithChildren) => {
    useRegisterNotification();
    // const register = async () => {
    //     await Promise.all([
    //         registerForPushNotificationsAsync(),
    //     ]);
    // };

    // useEffect(() => {
    //     if (isAuthenticated) {
    //         register();
    //     }
    // }, [isAuthenticated]);
    return <>{children}</>;
};

export default NotificationProvider;
