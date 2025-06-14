import React from "react";
import { Text, View } from "react-native";
import {
    Canvas,
    Path,
    notifyChange,
} from "@shopify/react-native-skia";
import {
    Gesture,
    GestureDetector,
} from "react-native-gesture-handler";
import { runOnJS } from "react-native-reanimated";

import { drawingStyles } from "../styles";
import { DrawingAreaProps } from "../types";

export const DrawingArea: React.FC<
    DrawingAreaProps
> = ({
    currentPath,
    hasStartedDrawing,
    onStartDrawing,
    canvasRef,
}) => {
    const pan = Gesture.Pan()
        .averageTouches(true)
        .maxPointers(1)
        .onBegin((e) => {
            "worklet";
            currentPath.value.moveTo(e.x, e.y);
            currentPath.value.lineTo(e.x, e.y);
            notifyChange(currentPath);
        })
        .onChange((e) => {
            "worklet";
            currentPath.value.lineTo(e.x, e.y);
            notifyChange(currentPath);
        })
        .onEnd(() => {
            "worklet";
            runOnJS(onStartDrawing)();
        });

    return (
        <>
            {!hasStartedDrawing && (
                <View
                    style={
                        drawingStyles.labelContainer
                    }
                >
                    <Text
                        style={
                            drawingStyles.signHereLabel
                        }
                    >
                        Sign Here
                    </Text>
                </View>
            )}
            <GestureDetector gesture={pan}>
                <Canvas
                    ref={canvasRef}
                    style={
                        drawingStyles.canvasStyle
                    }
                >
                    <Path
                        path={currentPath}
                        style="stroke"
                        strokeWidth={3}
                        strokeCap="round"
                        strokeJoin="round"
                        color="black"
                    />
                </Canvas>
            </GestureDetector>
        </>
    );
};
