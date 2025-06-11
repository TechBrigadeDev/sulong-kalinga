import { useToastController } from "@tamagui/toast";

export const useToast = () => {
    const toast = useToastController();

    const show = (message: string) => {
        toast.show(message, {
            burntOptions: {
                preset: "done",
            },
        });
    };

    const error = (message: string) => {
        toast.show(message, {
            burntOptions: {
                preset: "error",
            },
        });
    };

    return {
        show,
        error,
    };
};
