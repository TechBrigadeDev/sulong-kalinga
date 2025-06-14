import { drawingStyles } from "components/drawing/styles";
import { OrientationOverlayProps } from "components/drawing/types";
import React from "react";
import { Text, View } from "react-native";

export const OrientationOverlay: React.FC<
    OrientationOverlayProps
> = ({ isVisible }) => {
    if (!isVisible) return null;

    return (
        <View
            style={
                drawingStyles.orientationOverlay
            }
        >
            <View
                style={
                    drawingStyles.orientationContent
                }
            >
                <View
                    style={
                        drawingStyles.phoneIcon
                    }
                >
                    <Text
                        style={
                            drawingStyles.phoneIconText
                        }
                    >
                        📱
                    </Text>
                    <Text
                        style={
                            drawingStyles.rotateArrow
                        }
                    >
                        ↻
                    </Text>
                </View>
                <Text
                    style={
                        drawingStyles.orientationTitle
                    }
                >
                    Please rotate your device
                </Text>
                <Text
                    style={
                        drawingStyles.orientationSubtitle
                    }
                >
                    Turn your phone clockwise to
                    landscape mode for better
                    signing experience
                </Text>
            </View>
        </View>
    );
};
