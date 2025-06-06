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
            <XStack style={style.row} gap={10}>
                <XStack
                    gap={10}
                    style={style.linkLabel}
                >
                    <Icon />
                    <Text style={style.rowLabel}>
                        {label}
                    </Text>
                </XStack>
                <XStack
                    style={{ marginLeft: "auto" }}
                >
                    <Text>{value}</Text>
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
                    gap={10}
                    style={style.rowLabel}
                >
                    <Icon icon={icon} />
                    <Text style={style.rowLabel}>
                        {label}
                    </Text>
                </XStack>
                <XStack style={style.rowValue}>
                    {value && (
                        <Text>{value}</Text>
                    )}
                    <Chevron
                        size={24}
                        color="#000"
                        style={{
                            marginLeft: "auto",
                        }}
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
