import { Stack, useLocalSearchParams } from "expo-router";
import { Card, H4, Text, XStack } from "tamagui";

import TabScroll from "~/components/tabs/TabScroll";

interface ShiftDetailsProps {
    label: string;
    value: string;
}

const ShiftDetails = ({ label, value }: ShiftDetailsProps) => (
    <XStack style={{ marginBottom: 12 }}>
        <Text style={{ width: 140, color: "#666" }}>{label}</Text>
        <Text style={{ flex: 1 }}>{value}</Text>
    </XStack>
);

const Screen = () => {
    const { id: _id } = useLocalSearchParams();

    // Dummy data for the shift details
    const shiftDetails = {
        beneficiaryName: "John Doe",
        scheduleType: "Regular Visit",
        date: "00-00-0000",
        scheduledTime: "00:00 AM",
        beneficiaryAddress: "66 General Malvar Extension Barrio Jesus Dela Pena 1800, Marikina",
        hoursWorked: {
            timeArrived: "00:00 AM",
            timeDeparted: "00:00 PM",
            timeOnShift: "00:00 AM - 00:00 PM",
        },
        movementHistory: [
            {
                time: "00:00 AM",
                location: "66 General Malvar Extension Barrio Jesus Dela Pena 1800, Marikina",
            },
            { time: "00:00 PM", location: "Location departed" },
        ],
    };

    return (
        <TabScroll style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    title: "SHIFT DETAILS",
                    headerBackTitle: "",
                }}
            />

            <Card elevate style={{ margin: 16, padding: 16 }}>
                <H4 style={{ marginBottom: 16 }}>Beneficiary Details</H4>
                <ShiftDetails label="Beneficiary Name" value={shiftDetails.beneficiaryName} />
                <ShiftDetails label="Schedule Type" value={shiftDetails.scheduleType} />
                <ShiftDetails label="Date" value={shiftDetails.date} />
                <ShiftDetails label="Scheduled Time" value={shiftDetails.scheduledTime} />
                <ShiftDetails label="Beneficiary Address" value={shiftDetails.beneficiaryAddress} />

                <H4 style={{ marginTop: 24, marginBottom: 16 }}>Hours Worked</H4>
                <ShiftDetails label="Time Arrived" value={shiftDetails.hoursWorked.timeArrived} />
                <ShiftDetails label="Time Departed" value={shiftDetails.hoursWorked.timeDeparted} />
                <ShiftDetails label="Time On-Shift" value={shiftDetails.hoursWorked.timeOnShift} />

                <H4 style={{ marginTop: 24, marginBottom: 16 }}>Movement History</H4>
                {shiftDetails.movementHistory.map((movement, index) => (
                    <Card
                        key={index}
                        style={{
                            marginBottom:
                                index === shiftDetails.movementHistory.length - 1 ? 0 : 12,
                            padding: 12,
                            backgroundColor: "#f5f5f5",
                        }}
                    >
                        <Text style={{ fontWeight: "600", marginBottom: 4 }}>{movement.time}</Text>
                        <Text style={{ color: "#666", fontSize: 14 }}>{movement.location}</Text>
                    </Card>
                ))}
            </Card>
        </TabScroll>
    );
};

export default Screen;
