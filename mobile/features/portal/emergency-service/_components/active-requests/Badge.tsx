import { Text, XStack } from "tamagui";

interface BadgeProps {
    children: React.ReactNode;
    backgroundColor: string;
}

const Badge = ({
    children,
    backgroundColor,
}: BadgeProps) => (
    <XStack
        style={{
            backgroundColor,
            borderRadius: 12,
            paddingHorizontal: 8,
            paddingVertical: 4,
            alignItems: "center",
            justifyContent: "center",
        }}
    >
        <Text
            style={{
                color: "white",
                fontSize: 12,
                fontWeight: "600",
            }}
        >
            {children}
        </Text>
    </XStack>
);

export default Badge;
