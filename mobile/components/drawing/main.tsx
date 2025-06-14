import { CanvasRef } from "@shopify/react-native-skia";
import { useRouter } from "expo-router";
import React, {
    useContext,
    useRef,
    useState,
} from "react";
import { StyleSheet, View } from "react-native";

import {
    ButtonBar,
    DrawingArea,
    OrientationOverlay,
} from "./components";
import {
    useDrawingPath,
    useOrientation,
} from "./hooks";
import { DrawingContext } from "./store";

const DrawingCanvas: React.FC = () => {
    const store = useContext(DrawingContext);
    if (!store) {
        throw new Error(
            "DrawingContext not found. Make sure to wrap your component tree with DrawingProvider.",
        );
    }

    const { callBack } = store.getState();
    const router = useRouter();
    const canvasRef = useRef<CanvasRef>(null);

    // Use custom hooks
    const isLandscape = useOrientation();
    const {
        currentPath,
        hasStartedDrawing,
        clearPath,
        startDrawing,
    } = useDrawingPath();

    const [isSaving, setIsSaving] =
        useState(false);

    const handleBack = () => {
        router.back();
    };

    const handleClear = () => {
        clearPath();
    };

    const handleSave = async () => {
        setIsSaving(true);

        try {
            if (canvasRef.current && callBack) {
                // Use makeImageSnapshot to capture the Skia canvas
                const image =
                    await canvasRef.current.makeImageSnapshotAsync();

                if (image) {
                    // Convert to data URI
                    const data =
                        image.encodeToBase64();
                    const dataUri = `data:image/png;base64,${data}`;

                    callBack(dataUri);
                    handleClear();
                    handleBack();
                } else {
                    console.log(
                        "Failed to capture image from canvas",
                    );
                }
            }
        } catch (error) {
            console.error(
                "Error capturing signature:",
                error,
            );
        }
        setIsSaving(false);
    };

    return (
        <View style={StyleSheet.absoluteFill}>
            <OrientationOverlay
                isVisible={!isLandscape}
            />

            {isLandscape && (
                <>
                    <DrawingArea
                        currentPath={currentPath}
                        hasStartedDrawing={
                            hasStartedDrawing
                        }
                        onStartDrawing={
                            startDrawing
                        }
                        onPathChange={() => {}}
                        canvasRef={canvasRef}
                    />

                    <ButtonBar
                        onBack={handleBack}
                        onClear={handleClear}
                        onSave={handleSave}
                        isSaving={isSaving}
                    />
                </>
            )}
        </View>
    );
};

export default DrawingCanvas;
