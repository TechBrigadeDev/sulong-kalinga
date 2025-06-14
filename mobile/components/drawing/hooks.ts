import {
    notifyChange,
    Skia,
} from "@shopify/react-native-skia";
import { useEffect, useState } from "react";
import { Dimensions } from "react-native";
import { useSharedValue } from "react-native-reanimated";

export const useOrientation = () => {
    const [isLandscape, setIsLandscape] =
        useState(false);

    useEffect(() => {
        const updateOrientation = () => {
            const { width, height } =
                Dimensions.get("window");
            setIsLandscape(width > height);
        };

        updateOrientation();

        const subscription =
            Dimensions.addEventListener(
                "change",
                updateOrientation,
            );

        return () => subscription?.remove();
    }, []);

    return isLandscape;
};

export const useDrawingPath = () => {
    const currentPath = useSharedValue(
        Skia.Path.Make().moveTo(0, 0),
    );
    const [
        hasStartedDrawing,
        setHasStartedDrawing,
    ] = useState(false);

    const clearPath = () => {
        console.log(
            "Clear signature method called",
        );
        currentPath.value =
            Skia.Path.Make().moveTo(0, 0);
        notifyChange(currentPath);
        setHasStartedDrawing(false);
    };

    const startDrawing = () => {
        setHasStartedDrawing(true);
    };

    return {
        currentPath,
        hasStartedDrawing,
        clearPath,
        startDrawing,
    };
};
