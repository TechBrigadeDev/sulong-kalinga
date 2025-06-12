import { useToastController } from "@tamagui/toast";
import { showToastable } from "react-native-toastable";

import { log } from "./debug";

export const useToast = () => {
    const toast = useToastController();

    const show = (message: string) => {
        // Use both Tamagui toast and react-native-toastable for compatibility
        toast.show(message, {
            burntOptions: {
                preset: "done",
            },
        });

        showToastable({
            message,
            status: "success",
            duration: 3000,
        });
    };

    const error = (message: string) => {
        // Use both Tamagui toast and react-native-toastable for compatibility
        toast.show(message, {
            burntOptions: {
                preset: "error",
            },
        });

        showToastable({
            message,
            status: "danger",
            duration: 3000,
        });
    };

    return {
        show,
        error,
    };
};

export const toastServerError = (error: any) => {
    const message =
        "An unexpected server error occurred.\n" +
        "Please try again later or contact support if the issue persists.";

    log(
        "toastServerError",
        error,
        "message",
        message,
    );

    showToastable({
        message,
        status: "danger",
        duration: 6000,
    });
};
