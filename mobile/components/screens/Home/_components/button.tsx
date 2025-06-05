import { TabTriggerSlotProps } from "expo-router/ui";
import { icons } from "lucide-react-native";
import * as React from "react";
import { Pressable, StyleSheet, Text, View } from "react-native";

interface CustomTabButtonProps extends React.PropsWithChildren, TabTriggerSlotProps {
    icon: keyof typeof icons;
    onPress?: () => void;
}

const TabButton = React.forwardRef<View, CustomTabButtonProps>((props, ref) => {
    const { icon, onPress } = props;

    const Icon = icons[icon];

    const handlePress = () => {
        if (onPress) {
            onPress();
        }
    };

    return (
        <Pressable ref={ref} {...props} style={styles.button} onPress={handlePress}>
            <Icon size={28} color={props.isFocused ? "#000" : "#64748B"} />
            <Text style={[styles.text, props.isFocused && styles.focusedText]}>
                {props.children}
            </Text>
        </Pressable>
    );
});

TabButton.displayName = "TabButton";

const styles = StyleSheet.create({
    button: {
        justifyContent: "center",
        alignItems: "center",
        borderColor: "#7a7777",
    },
    focusedText: {
        color: "#000",
        fontSize: 12,
        fontWeight: "500",
    },
    text: {
        color: "#64748B",
        fontSize: 12,
        marginTop: 4,
        fontWeight: "500",
    },
});

export default TabButton;
