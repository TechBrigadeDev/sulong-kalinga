import { Stack } from "expo-router";
import { Card, Text, XStack, YStack } from "tamagui";

import TabScroll from "~/components/tabs/TabScroll";

const WorkHistoryCard = ({
    beneficiary,
    date,
    hours,
    onPress,
}: {
    beneficiary: string;
    date: string;
    hours: string;
    onPress?: () => void;
}) => (
    <Card
        elevate
        pressStyle={{ opacity: 0.8 }}
        onPress={onPress}
        style={{ marginBottom: 12, padding: 16 }}
    >
        <XStack style={{ alignItems: "center" }}>
            <YStack style={{ flex: 1 }}>
                <Text style={{ fontWeight: "600", marginBottom: 4 }}>{beneficiary}</Text>
                <Text style={{ color: "#666", fontSize: 14 }}>{date}</Text>
            </YStack>
            <XStack style={{ alignItems: "center", gap: 8 }}>
                <Text style={{ color: "#666", fontSize: 14 }}>{hours}</Text>
                <Text style={{ color: "#666" }}>👁️</Text>
            </XStack>
        </XStack>
    </Card>
);

const Screen = () => {
    // Dummy data for work history
    const history = [
        { id: 1, beneficiary: "John Doe", date: "00-00-0000", hours: "00:00 AM - 00:00 PM" },
        { id: 2, beneficiary: "Jane Smith", date: "00-00-0000", hours: "00:00 AM - 00:00 PM" },
        { id: 3, beneficiary: "Bob Johnson", date: "00-00-0000", hours: "00:00 AM - 00:00 PM" },
        { id: 4, beneficiary: "Alice Brown", date: "00-00-0000", hours: "00:00 AM - 00:00 PM" },
        { id: 5, beneficiary: "Charlie Wilson", date: "00-00-0000", hours: "00:00 AM - 00:00 PM" },
    ];

    return (
        <TabScroll style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    title: "VIEW WORK HISTORY",
                    headerBackTitle: "",
                }}
            />
            <YStack style={{ padding: 16 }}>
                {history.map((item) => (
                    <WorkHistoryCard
                        key={item.id}
                        beneficiary={item.beneficiary}
                        date={item.date}
                        hours={item.hours}
                        onPress={() => {}}
                    />
                ))}
            </YStack>
        </TabScroll>
    );
};

export default Screen;
