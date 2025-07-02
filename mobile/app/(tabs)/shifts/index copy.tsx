import AvatarImage from "components/Avatar";
import TabScroll from "components/tabs/TabScroll";
import { Link, Stack } from "expo-router";
import { useState, useEffect } from "react";
import { SafeAreaView } from "react-native-safe-area-context";
import * as Location from 'expo-location';
import {
    Avatar,
    Button,
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";
import { authStore } from "../../../features/auth/auth.store";
import { useShiftStore } from "../../../features/auth/shift.store";

const VisitationCard = ({
    name,
    address,
    time,
    type = "Regular Visit",
    onArrive,
    onDepart,
}: {
    name: string;
    address: string;
    time: string;
    type?: string;
    onArrive?: () => void;
    onDepart?: () => void;
}) => (
    <Card
        elevate
        style={{ marginBottom: 12, padding: 16 }}
    >
        <YStack>
            <XStack style={{ marginBottom: 8 }}>
                <Text
                    style={{
                        flex: 1,
                        fontWeight: "600",
                    }}
                >
                    {name}
                </Text>
                <Card
                    elevate
                    style={{
                        backgroundColor:
                            type ===
                                "Regular Visit"
                                ? "#0077FF"
                                : "#00AA00",
                        borderRadius: 20,
                        paddingHorizontal: 8,
                        paddingVertical: 4,
                    }}
                >
                    <Text
                        style={{
                            color: "white",
                            fontSize: 12,
                        }}
                    >
                        {type}
                    </Text>
                </Card>
            </XStack>
            <XStack
                style={{
                    marginBottom: 8,
                    alignItems: "center",
                    gap: 8,
                }}
            >
                <Text style={{ color: "#666" }}>
                    📍
                </Text>
                <Text
                    style={{
                        flex: 1,
                        color: "#666",
                        fontSize: 14,
                    }}
                >
                    {address}
                </Text>
            </XStack>
            <XStack
                style={{
                    marginBottom: 12,
                    alignItems: "center",
                    gap: 8,
                }}
            >
                <Text style={{ color: "#666" }}>
                    🕒
                </Text>
                <Text
                    style={{
                        color: "#666",
                        fontSize: 14,
                    }}
                >
                    {time}
                </Text>
            </XStack>
            <XStack style={{ gap: 8 }}>
                <Button
                    style={{ flex: 1 }}
                    onPress={onArrive}
                >
                    Arrived
                </Button>
                <Button
                    style={{ flex: 1 }}
                    onPress={onDepart}
                >
                    Departed
                </Button>
            </XStack>
        </YStack>
    </Card>
);

const Screen = () => {
    const [isOnShift, setOnShift] =
        useState(useShiftStore((state) => state.isOnShift));
    const [currentShift, setCurrentShift] = useState(useShiftStore((state) => state.currentShift));
    const [shifts, setShifts] = useState([]);
    const [error, setError] = useState('');
    const API_URL = process.env.EXPO_PUBLIC_API_URL;
    const [location, setLocation] = useState(null);
    const [errorMsg, setErrorMsg] = useState(null);

    useEffect(() => {

        const fetchShifts = async () => {
            try {
                const token = authStore.getState().token; // ⬅️ get the token from the store

                const response = await fetch(`${API_URL}/shifts`, {
                    method: "GET",
                    headers: {
                        "Authorization": `Bearer ${token}`, // ⬅️ Required for Laravel Sanctum token auth
                        "Accept": "application/json",
                    },
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                console.log(data);
                setShifts(data);
            } catch (err) {
                console.error('Error fetching shifts:', err);
                setError('Failed to load shifts.');
            }
        };

        fetchShifts();
    }, []);


    const ArrivedShift = async ({
        care_worker_id,
        visitation_id,
        coordinates,
    }: {
        care_worker_id: number;
        visitation_id: number;
        coordinates: { lat: number; lng: number };
    }) => {
        try {
            const token = authStore.getState().token;

            const payload = {
                care_worker_id,
                // visitation_id,
                track_coordinates: coordinates,
                // recorded_at: new Date().toISOString(), // current time
            };

            const response = await fetch(`${API_URL}/shifts/time-in`, {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            setCurrentShift(data);
            console.log("Time in success:", data);
            return data;
        } catch (err) {
            console.error("Error during time-in:", err);
            throw err;
        }
    };
    const DepartedShift = async ({
        shift_id,
        care_worker_id,
        visitation_id,
        coordinates,
    }: {
        shift_id: number;
        care_worker_id: number;
        visitation_id: number;
        coordinates: { lat: number; lng: number };
    }) => {
        try {
            const token = authStore.getState().token;

            const payload = {
                care_worker_id,
                visitation_id,
                track_coordinates: coordinates,
                recorded_at: new Date().toISOString(),
            };

            const response = await fetch(`${API_URL}/shifts/${shift_id}/time-out`, {
                method: "PATCH",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            console.log("Time out success:", data);
            return data;
        } catch (err) {
            console.error("Error during time-out:", err);
            throw err;
        }
    };

    const timeInShift = async () => {
        const token = authStore.getState().token;

        const coords = location;

        const response = await fetch(`${API_URL}/shifts/time-in`, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                track_coordinates: coords,
            }),
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        console.log(data)
        useShiftStore.getState().setOnShift(true);
        useShiftStore.getState().setCurrentShift(data);

        console.log("✅ Time-in shift with location:", data);
        return data;
    };

    const getCurrentLocation = async () => {
        let { status } = await Location.requestForegroundPermissionsAsync();
        if (status !== 'granted') {
            setErrorMsg('Permission to access location was denied');
            return;
        }

        let loc = await Location.getCurrentPositionAsync({});
        console.log(loc);
        setLocation(loc.coords); // Only saving coordinates
    };


    const timeOutShift = async ({
        shift_id,
        care_worker_id,
    }: {
        shift_id: number;
        care_worker_id: number;
    }) => {
        const token = authStore.getState().token;
        const payload = {
            care_worker_id,
            shift_id,
            recorded_at: new Date().toISOString(),
        };
        console.log(API_URL)
        const response = await fetch(`${API_URL}/shifts/${shift_id}/time-out`, {
            method: "PATCH",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });
        console.log(response)
        // Clear in-memory store
        useShiftStore.getState().setOnShift(false);
        useShiftStore.getState().setCurrentShift(null);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();



        return data;
    };


    // ✅ Call once on mount if needed
    useEffect(() => {
        getCurrentLocation();
    }, []);

    // ✅ Use inside shiftEvent
    const shiftEvent = (event: boolean) => {
        console.log("currentShift",currentShift)
        console.log("event",event)
        // if (!event) {
        //     timeOutShift({
        //         shift_id: 3,
        //         care_worker_id: 11,
        //     })
        // } else {
            timeInShift();
        // }
        setOnShift(event);
       
    };



    return (
        <TabScroll style={{ flex: 1 }}>
            <YStack style={{ padding: 16 }}>
                <Card
                    elevate
                    style={{
                        marginBottom: 16,
                        padding: 16,
                    }}
                >
                    <YStack>
                        <H4
                            style={{
                                marginBottom: 16,
                            }}
                        >
                            CARE WORKER ACTIVITY
                        </H4>
                        <XStack
                            style={{
                                gap: 16,
                                marginBottom: 16,
                            }}
                        >
                            <Avatar
                                circular
                                size="$4"
                            >
                                <AvatarImage
                                    uri="https://placekitten.com/200/200"
                                    fallback="CW"
                                />
                            </Avatar>
                            <YStack
                                style={{
                                    flex: 1,
                                    justifyContent:
                                        "center",
                                }}
                            >
                                <XStack
                                    style={{
                                        alignItems:
                                            "center",
                                        gap: 8,
                                    }}
                                >
                                    <Text>
                                        Status:
                                    </Text>
                                    <Card
                                        elevate
                                        style={{
                                            backgroundColor:
                                                isOnShift
                                                    ? "#00AA00"
                                                    : "#666",
                                            borderRadius: 20,
                                            paddingHorizontal: 8,
                                            paddingVertical: 4,
                                        }}
                                    >
                                        <Text
                                            style={{
                                                color: "white",
                                                fontSize: 12,
                                            }}
                                        >
                                            {isOnShift
                                                ? "On-Shift"
                                                : "Off-Shift"}
                                        </Text>
                                    </Card>
                                </XStack>
                                <XStack
                                    style={{
                                        gap: 8,
                                        marginTop: 8,
                                    }}
                                >
                                    <Button
                                        style={{
                                            flex: 1,
                                            backgroundColor:
                                                isOnShift
                                                    ? "#FF4444"
                                                    : "#00AA00",
                                        }}
                                        onPress={() =>
                                            shiftEvent(
                                                !isOnShift,
                                            )
                                        }
                                    >
                                        {isOnShift
                                            ? "END Shift"
                                            : "START Shift"}
                                    </Button>
                                    <Link
                                        href="/shifts/work-history"
                                        asChild
                                    >
                                        <Button
                                            style={{
                                                flex: 1,
                                                backgroundColor:
                                                    "#0077FF",
                                            }}
                                        >
                                            VIEW
                                            Work
                                            History
                                        </Button>
                                    </Link>
                                </XStack>
                            </YStack>
                        </XStack>
                    </YStack>
                </Card>

                <H4 style={{ marginBottom: 16 }}>
                    Scheduled Visitations
                </H4>

                {/* Dummy data for scheduled visitations */}
                {/* <VisitationCard
                    name="John Doe"
                    address="66 General Malvar Extension Barrio Jesus Dela Pena 1800, Marikina"
                    time="09:00 AM"
                    type="Regular Visit"
                />

                <VisitationCard
                    name="Jane Smith"
                    address="123 Sample Street, Marikina"
                    time="02:00 PM"
                    type="Service Request"
                /> */}
                {shifts.map((item) => (
                    <VisitationCard
                        key={item.visitation_id} // Always provide a unique key
                        name={item.beneficiary_name}
                        address={item.address}
                        time={
                            item.start_time
                                ? new Date(item.start_time).toLocaleTimeString([], {
                                    hour: "2-digit",
                                    minute: "2-digit",
                                })
                                : "Flexible"
                        }
                        type={
                            item.visit_type === "routine_care_visit"
                                ? "Routine Visit"
                                : "Other"
                        }
                        onArrive={() =>
                            console.log("Arrived at visitation:", item.visitation_id)
                        }
                        onDepart={() =>
                            console.log("Departed from visitation:", item.visitation_id)
                        }
                    />
                ))}

            </YStack>
        </TabScroll>
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                headerShown: false,
            }}
        />
        <SafeAreaView style={{ flex: 1 }}>
            <Screen />
        </SafeAreaView>
    </>
);

export default Layout;
