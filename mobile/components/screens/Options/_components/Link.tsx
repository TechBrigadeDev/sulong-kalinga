import {
    Link as ExpoLink,
    LinkProps,
} from "expo-router";
import { icons } from "lucide-react-native";
import React from "react";
import {
    StyleSheet,
    TouchableOpacity,
} from "react-native";
import { Text, XStack } from "tamagui";

export const Link = ({
    href,
    label,
    icon,
}: {
    href: LinkProps["href"];
    label: string;
    icon: keyof typeof icons;
}) => {
    const Icon = icons[icon];
    const Chevron = icons.ChevronRight;

    return (
        <ExpoLink href={href} asChild>
            <TouchableOpacity style={style.link}>
                <XStack
                    gap={10}
                    style={style.linkLabel}
                >
                    <Icon
                        size={24}
                        color="#000"
                    />
                    <Text>{label}</Text>
                </XStack>

                <Chevron
                    size={24}
                    color="#000"
                    style={{ marginLeft: "auto" }}
                />
            </TouchableOpacity>
        </ExpoLink>
    );
};

const style = StyleSheet.create({
    link: {
        display: "flex",
        flexDirection: "row",
        gap: 10,
        paddingVertical: 15,
    },
    linkLabel: {
        alignItems: "center",
    },
});
