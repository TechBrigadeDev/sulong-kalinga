import React from "react";
import {
    Platform,
    TouchableNativeFeedback,
    TouchableOpacity,
    View,
} from "react-native";

interface Props {
    onPress: () => void;
    disabled?: boolean;
    style?: object;
    children: React.ReactNode;
    rippleColor?: string;
}

const Touchable = ({
    onPress,
    disabled = false,
    style,
    children,
    rippleColor = "rgba(0,0,0,0.2)",
}: Props) => {
    if (Platform.OS === "android") {
        return (
            <TouchableNativeFeedback
                onPress={onPress}
                background={TouchableNativeFeedback.Ripple(
                    rippleColor,
                    false,
                )}
                disabled={disabled}
            >
                <View style={style}>
                    {children}
                </View>
            </TouchableNativeFeedback>
        );
    }
    return (
        <TouchableOpacity
            onPress={onPress}
            style={style}
            activeOpacity={0.7}
            disabled={disabled}
        >
            {children}
        </TouchableOpacity>
    );
};

export default Touchable;
