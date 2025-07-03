import {
    Link as ExpoLink,
    LinkProps,
} from "expo-router";
import { icons } from "lucide-react-native";
import {
    StyleSheet,
    TouchableOpacity,
} from "react-native";
import { Text, XStack } from "tamagui";

const OptionRow = ({
    href,
    label,
    value,
    icon,
}: {
    label: string;
    value?: string;
    href?: LinkProps["href"];
    icon?: keyof typeof icons;
}) => {
    if (!href) {
        return (
            <XStack
                p="$3"
                gap="$3"
                style={{
                    flexDirection: "row",
                    alignItems: "center",
                    justifyContent:
                        "space-between",
                    minHeight: 60,
                }}
            >
                <XStack
                    gap="$3"
                    style={{
                        flexDirection: "row",
                        alignItems: "center",
                        flex: 1,
                    }}
                >
                    <Icon />
                    <Text
                        style={style.rowLabel}
                        numberOfLines={1}
                        ellipsizeMode="tail"
                    >
                        {label}
                    </Text>
                </XStack>
                <XStack
                    style={{
                        maxWidth: "60%",
                        alignItems: "flex-end",
                    }}
                >
                    <Text
                        textWrap="wrap"
                        numberOfLines={0}
                        fontSize="$4"
                        style={{
                            flexShrink: 1,
                            textAlign: "right",
                        }}
                    >
                        {value}
                    </Text>
                </XStack>
            </XStack>
        );
    }

    return (
        <Link
            href={href}
            label={label}
            value={value}
            icon={icon}
        />
    );
};

const Link = ({
    href,
    label,
    value,
    icon,
}: {
    href: LinkProps["href"];
    label: string;
    value?: string;
    icon?: keyof typeof icons;
}) => {
    const Chevron = icons.ChevronRight;

    return (
        <ExpoLink href={href} asChild>
            <TouchableOpacity style={style.row}>
                <XStack
                    gap="$3"
                    style={{
                        ...style.rowLabel,
                        flex: 1,
                    }}
                >
                    <Icon icon={icon} />
                    <Text
                        style={style.rowLabel}
                        numberOfLines={1}
                        ellipsizeMode="tail"
                    >
                        {label}
                    </Text>
                </XStack>
                <XStack
                    gap="$2"
                    style={{
                        ...style.rowValue,
                        maxWidth: "60%",
                        alignItems: "center",
                    }}
                >
                    {value && (
                        <Text
                            numberOfLines={1}
                            ellipsizeMode="tail"
                            style={{
                                flexShrink: 1,
                                textAlign:
                                    "right",
                            }}
                        >
                            {value}
                        </Text>
                    )}
                    <Chevron
                        size={24}
                        color="#000"
                    />
                </XStack>
            </TouchableOpacity>
        </ExpoLink>
    );
};

const Icon = ({
    icon,
}: {
    icon?: keyof typeof icons;
}) => {
    if (!icon) {
        return null;
    }
    const IconComponent = icons[icon];
    return (
        <IconComponent size={24} color="#000" />
    );
};

const style = StyleSheet.create({
    row: {
        display: "flex",
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingVertical: 15,
        paddingHorizontal: 15,
    },
    rowLabel: {
        fontWeight: "bold",
    },
    rowValue: {
        justifyContent: "center",
    },
    linkLabel: {
        flexDirection: "row",
        alignItems: "center",
    },
});

export default OptionRow;
