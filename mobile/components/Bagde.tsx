import { PropsWithChildren } from "react";
import { Text, XStack } from "tamagui";

interface BadgeProps {
    variant?:
        | "success"
        | "warning"
        | "error"
        | "info"
        | "ghost"
        | "default";
    color?: string;
    backgroundColor?: string;
    borderColor?: string;
    size?: number;
    fontWeight?:
        | 100
        | 200
        | 300
        | 400
        | 500
        | 600
        | 700
        | 800
        | 900;
    borderRadius?: number;
    paddingHorizontal?: number;
    paddingVertical?: number;
    style?: any;
    children: React.ReactNode;
}

const VARIANT_STYLES: Record<
    string,
    {
        backgroundColor: string;
        color: string;
        borderColor: string;
    }
> = {
    success: {
        backgroundColor: "#22c55e",
        color: "#fff",
        borderColor: "#16a34a",
    },
    warning: {
        backgroundColor: "#facc15",
        color: "#92400e",
        borderColor: "#eab308",
    },
    error: {
        backgroundColor: "#ef4444",
        color: "#fff",
        borderColor: "#b91c1c",
    },
    info: {
        backgroundColor: "#38bdf8",
        color: "#fff",
        borderColor: "#0ea5e9",
    },
    ghost: {
        backgroundColor: "transparent",
        color: "#64748b",
        borderColor: "#cbd5e1",
    },
    default: {
        backgroundColor: "#e5e7eb",
        color: "#111827",
        borderColor: "#d1d5db",
    },
};

const Badge = ({
    variant = "default",
    color,
    backgroundColor,
    borderColor,
    size = 13,
    fontWeight = 700,
    borderRadius = 999,
    paddingHorizontal = 10,
    paddingVertical = 2,
    style = {},
    children,
}: PropsWithChildren<BadgeProps>) => {
    const variantStyle =
        VARIANT_STYLES[variant] ||
        VARIANT_STYLES.default;
    return (
        <XStack
            borderWidth={1}
            style={{
                minWidth: 24,
                minHeight: 20,
                maxWidth: "100%",
                flexShrink: 1,
                justifyContent: "center",
                alignItems: "center",
                backgroundColor:
                    backgroundColor ??
                    variantStyle.backgroundColor,
                borderColor:
                    borderColor ??
                    variantStyle.borderColor,
                borderRadius,
                paddingHorizontal,
                paddingVertical,
                ...style,
            }}
        >
            <Text
                style={{
                    color:
                        color ??
                        variantStyle.color,
                    fontSize: size,
                    fontWeight,
                }}
                numberOfLines={1}
                ellipsizeMode="tail"
            >
                {children}
            </Text>
        </XStack>
    );
};

export default Badge;
