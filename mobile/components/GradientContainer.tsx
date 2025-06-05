import { Canvas, Fill, interpolateColors, LinearGradient, vec } from "@shopify/react-native-skia";
import React, { useEffect } from "react";
import { StyleSheet, useWindowDimensions, View, ViewProps } from "react-native";
import { useDerivedValue, useSharedValue, withRepeat, withTiming } from "react-native-reanimated";

interface AnimatedGradientBackgroundProps extends ViewProps {
    colors?: string[];
    animationDuration?: number;
}

export default function GradientBackground({
    children,
    style,
    colors = ["#ff0080", "#ff8c00", "#40e0d0"],
    animationDuration = 8000,
    ...rest
}: AnimatedGradientBackgroundProps) {
    const { width, height } = useWindowDimensions();

    const progress = useSharedValue(0);
    useEffect(() => {
        progress.value = withRepeat(withTiming(1, { duration: animationDuration }), -1, true);
    }, [animationDuration, progress]);

    const animatedColors = useDerivedValue(() =>
        colors.map((c, i, arr) =>
            interpolateColors(progress.value, [0, 1], [arr[i], arr[(i + 1) % arr.length]]),
        ),
    );

    const translate = useDerivedValue(() => [
        { translateX: progress.value * width },
        { translateY: -progress.value * height },
    ]);

    return (
        <View style={[styles.container, style]} {...rest}>
            <Canvas style={StyleSheet.absoluteFill}>
                <Fill>
                    <LinearGradient
                        start={vec(0, 0)}
                        end={vec(width * 1.5, height * 1.5)}
                        colors={animatedColors}
                        transform={translate}
                    />
                </Fill>
            </Canvas>
            {children}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        overflow: "hidden",
    },
});
