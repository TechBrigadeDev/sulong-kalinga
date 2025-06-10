import {
    ToastViewport,
    useToastState,
} from "@tamagui/toast";
import { useSafeAreaInsets } from "react-native-safe-area-context";

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
