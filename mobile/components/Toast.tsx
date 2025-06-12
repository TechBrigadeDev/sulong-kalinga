import {
    ToastViewport,
    useToastState,
} from "@tamagui/toast";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import Toastable from "react-native-toastable";

export const TOASTS = {
    GLOBAL: "global",
};

export const GlobalToast = () => {
    const toast = useToastState();
    const { left, top, right } =
        useSafeAreaInsets();

    console.log(
        "GlobalToast currentToast:",
        toast,
    );
    if (!toast) {
        return null;
    }

    return (
        <ToastViewport
            left={left}
            top={top}
            right={right}
            flexDirection="column-reverse"
            multipleToasts
        />
    );
};

export const Toast = () => {
    const { top } = useSafeAreaInsets();

    return (
        <Toastable
            statusMap={{
                success: "#00BFA6",
                danger: "#FF5252",
                warning: "#FFD600",
                info: "#2962FF",
            }}
            offset={top}
            position="top"
            swipeDirection="right"
        />
    );
};
