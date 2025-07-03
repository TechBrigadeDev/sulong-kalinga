import { useFocusEffect } from "@react-navigation/native";
import AvatarImage from "components/Avatar";
import TabScroll from "components/tabs/TabScroll";
import * as Location from "expo-location";
import { Stack } from "expo-router";
import { authStore } from "features/auth/auth.store";
import { useShiftStore } from "features/auth/shift.store";
import React, {
    useCallback,
    useEffect,
    useState,
} from "react";
import { Alert } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { showToastable } from "react-native-toastable";
import {
    Avatar,
    Button,
    Card,
    H4,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { secureStorage } from "~/common/storage/secure.store";
const buttonBaseStyle = {
    flex: 1,
    padding: 12,
    borderRadius: 8,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#007bff",
};

const disabledButtonStyle = {
    backgroundColor: "#ccc",
    opacity: 0.6,
};

const VisitationCard = ({
    name,
    address,
    time,
    type = "Regular Visit",
    isArrive,
    onArrive,
    onDepart,
}: {
    name: string;
    address: string;
    time: string;
    type?: string;
    isArrive: any;
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
                    style={[
                        buttonBaseStyle,
                        (isArrive === "arrived" ||
                            isArrive ===
                                "departed") &&
                            disabledButtonStyle,
                    ]}
                    onPress={onArrive}
                    disabled={
                        isArrive === "arrived" ||
                        isArrive === "departed"
                    }
                >
                    Arrived
                </Button>

                <Button
                    style={[
                        buttonBaseStyle,
                        (!isArrive ||
                            isArrive ===
                                "departed") &&
                            disabledButtonStyle,
                    ]}
                    onPress={onDepart}
                    disabled={
                        !isArrive ||
                        isArrive === "departed"
                    }
                >
                    Departed
                </Button>
            </XStack>
        </YStack>
    </Card>
);

const Screen = () => {
    const [
        currentShiftFetched,
        setCurrentShiftFetched,
    ] = useState([]);
    const [shifts, setShifts] = useState([]);
    const [error, setError] = useState("");
    const API_URL =
        process.env.EXPO_PUBLIC_API_URL;
    const [location, setLocation] =
        useState(null);
    const [errorMsg, setErrorMsg] =
        useState(null);
    const fetchShifts = async () => {
        try {
            const token =
                authStore.getState().token; // ⬅️ get the token from the store

            const response = await fetch(
                `${API_URL}/shifts`,
                {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`, // ⬅️ Required for Laravel Sanctum token auth
                        Accept: "application/json",
                    },
                },
            );

            if (!response.ok)
                throw new Error(
                    `HTTP ${response.status}`,
                );

            const data = await response.json();
            console.log("shifts", data);
            setShifts(data);
        } catch (err) {
            console.error(
                "Error fetching shifts:",
                err,
            );
            setError("Failed to load shifts.");
        }
    };
    const fetchCurrentShifts = async () => {
        try {
            const token =
                authStore.getState().token;
            const response = await fetch(
                `${API_URL}/shifts/current`,
                {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                },
            );

            const data = await response.json();
            console.log(
                "current shifts fetched",
                data,
            );
            setCurrentShiftFetched(data);
        } catch (err) {
            console.error(
                "Error fetching shifts:",
                err,
            );
        }
    };
    const getCurrentLocation = async () => {
        let { status } =
            await Location.requestForegroundPermissionsAsync();
        if (status !== "granted") {
            setErrorMsg(
                "Permission to access location was denied",
            );
            return null;
        }

        let loc =
            await Location.getCurrentPositionAsync(
                {},
            );
        console.log(loc);
        setLocation(loc.coords);
        return loc.coords;
    };
    useEffect(() => {
        const intervalId = setInterval(
            fetchShifts,
            20000,
        ); // Fetch every 20 seconds

        return () => clearInterval(intervalId); // Cleanup on unmount
    }, []);

    useFocusEffect(
        useCallback(() => {
            getCurrentLocation();
        }, []),
    );

    useEffect(() => {
        getCurrentLocation();
        fetchShifts();
        fetchCurrentShifts();
    }, []);

    const ArrivedShift = async ({
        care_worker_id,
        visitation_id,
        coordinates,
        shift_id,
    }: {
        care_worker_id: number;
        shift_id: number;
        visitation_id: number;
        coordinates: { lat: number; lng: number };
    }) => {
        try {
            const token =
                authStore.getState().token;
            const now = new Date();
            const localDate =
                now.toLocaleDateString("en-CA");

            const payload = {
                care_worker_id,
                visitation_id,
                track_coordinates: coordinates,
                recorded_at: localDate, // current time
                arrival_status: "arrived", // or "departed"
            };

            const response = await fetch(
                `${API_URL}/shifts/${shift_id}/tracks/event`,
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                        "Content-Type":
                            "application/json",
                    },
                    body: JSON.stringify(payload),
                },
            );

            if (!response.ok) {
                const errorBody =
                    await response.json();

                // Try extracting a detailed error:
                let errorMessage =
                    errorBody.message ||
                    `Error ${response.status}`;

                if (
                    errorBody.errors &&
                    typeof errorBody.errors ===
                        "object"
                ) {
                    const firstField =
                        Object.keys(
                            errorBody.errors,
                        )[0];
                    errorMessage =
                        errorBody.errors[
                            firstField
                        ]?.[0] || errorMessage;
                }

                showToastable({
                    title: "Error",
                    status: "danger",
                    message: errorMessage,
                });
                return;
                // throw new Error(errorMessage);
            }

            const data = await response.json();
            showToastable({
                title: "Arrived Successfully",
                status: "success",
                message: "Arrived success",
            });
            fetchShifts();
            return data;
        } catch (err) {
            console.error(
                "Error during time-in:",
                err,
            );
            // throw err;
        }
    };
    const DepartedShift = async ({
        care_worker_id,
        visitation_id,
        coordinates,
        shift_id,
    }: {
        shift_id: number;
        care_worker_id: number;
        visitation_id: number;
        coordinates: { lat: number; lng: number };
    }) => {
        try {
            const token =
                authStore.getState().token;
            const now = new Date();
            const localDate =
                now.toLocaleDateString("en-CA");
            const payload = {
                care_worker_id,
                visitation_id,
                track_coordinates: coordinates,
                recorded_at: localDate,
                arrival_status: "departed", // or "departed"
            };

            const response = await fetch(
                `${API_URL}/shifts/${shift_id}/tracks/event`,
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                        "Content-Type":
                            "application/json",
                    },
                    body: JSON.stringify(payload),
                },
            );

            if (!response.ok) {
                const errorBody =
                    await response.json();

                // Try extracting a detailed error:
                let errorMessage =
                    errorBody.message ||
                    `Error ${response.status}`;

                if (
                    errorBody.errors &&
                    typeof errorBody.errors ===
                        "object"
                ) {
                    const firstField =
                        Object.keys(
                            errorBody.errors,
                        )[0];
                    errorMessage =
                        errorBody.errors[
                            firstField
                        ]?.[0] || errorMessage;
                }
                showToastable({
                    title: "Error",
                    status: "danger",
                    message: errorMessage,
                });
                return;

                // throw new Error(errorMessage);
            }

            const data = await response.json();
            showToastable({
                title: "Departed Successfully",
                status: "success",
                message: "Departed success",
            });
            fetchShifts();
            console.log(
                "Time out success:",
                data,
            );
            return data;
        } catch (err) {
            console.error(
                "Error during time-out:",
                err,
            );
            // throw err;
        }
    };

    const timeInShift = async () => {
        const token = authStore.getState().token;
        const coords = location;

        const response = await fetch(
            `${API_URL}/shifts/time-in`,
            {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type":
                        "application/json",
                },
                body: JSON.stringify({
                    track_coordinates: coords,
                }),
            },
        );

        const text = await response.text();

        if (!response.ok) {
            console.error("❌ Error Body:", text);

            // Try to parse the message
            try {
                const json = JSON.parse(text);

                // Handle known conflict
                if (
                    response.status === 409 &&
                    json?.message ===
                        "Care worker already has an in-progress shift."
                ) {
                    console.log(
                        "⚠️ Already clocked in. Restoring shift state...",
                    );
                    await secureStorage.setItem(
                        "isOnShift",
                        "true",
                    );
                    await secureStorage.setItem(
                        "currentShift",
                        JSON.stringify(data),
                    );

                    return null; // Or return a fallback if needed
                }
            } catch (e) {
                console.error(
                    "❌ Failed to parse error JSON:",
                    e,
                );
            }

            // throw new Error(`HTTP ${response.status}: ${text}`);
        }

        const data = JSON.parse(text);

        useShiftStore.getState().setOnShift(true);
        useShiftStore
            .getState()
            .setCurrentShift(data);

        await secureStorage.setItem(
            "isOnShift",
            "true",
        );
        await secureStorage.setItem(
            "currentShift",
            JSON.stringify(data),
        );
        showToastable({
            title: "Success",
            status: "success",
            message: "Time-in successfully.",
        });
        console.log(
            "✅ Time-in shift with location:",
            data,
        );
        fetchCurrentShifts();
        return data;
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
            time_out: new Date().toISOString(), // ✅ Laravel expects this field
        };

        const response = await fetch(
            `${API_URL}/shifts/${shift_id}/time-out`,
            {
                method: "PATCH",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type":
                        "application/json",
                },
                body: JSON.stringify(payload), // ✅ Add this — you're currently not sending anything!
            },
        );

        const data = await response.json();

        if (!response.ok) {
            // Handle error
            console.log("errorBody", data);
            let errorMessage =
                data.message ||
                `Error ${response.status}`;

            if (
                data.errors &&
                typeof data.errors === "object"
            ) {
                const firstKey = Object.keys(
                    data.errors,
                )[0];
                if (
                    firstKey &&
                    Array.isArray(
                        data.errors[firstKey],
                    )
                ) {
                    errorMessage =
                        data.errors[firstKey][0];
                }
            }

            showToastable({
                title: "Error",
                status: "danger",
                message: errorMessage,
            });

            return; // Exit early on error
        }

        // ✅ Success — show success toast
        showToastable({
            title: "Success",
            status: "success",
            message:
                "Shift timed out successfully.",
        });
        fetchShifts();
        console.log(
            "Shift timeout success:",
            data,
        );
        return data;
    };

    // ✅ Use inside shiftEvent
    const useShiftEvent = (
        location: { lat: number; lng: number },
        currentShiftFetched?: any,
    ) => {
        const isOnShift = useShiftStore(
            (state) => state.isOnShift,
        );
        const currentShift = useShiftStore(
            (state) => state.currentShift,
        );
        const setOnShift = useShiftStore(
            (state) => state.setOnShift,
        );
        const setCurrentShift = useShiftStore(
            (state) => state.setCurrentShift,
        );

        // Sync fetched shift data into the global store
        useEffect(() => {
            if (!currentShiftFetched) return;

            if (
                currentShiftFetched?.message ===
                "No in-progress shift found."
            ) {
                setOnShift(false);
                setCurrentShift(null);
            } else {
                setOnShift(true);
                setCurrentShift(
                    currentShiftFetched,
                );
            }
        }, [currentShiftFetched]);

        const shiftEvent = async (
            event: boolean,
        ) => {
            try {
                console.log(
                    "Current shift:",
                    currentShift,
                );
                console.log(
                    "Event (true=time-in, false=time-out):",
                    event,
                );

                if (event) {
                    const result =
                        await timeInShift(
                            location,
                        );
                    if (result) {
                        setOnShift(true);
                        setCurrentShift(result);
                    }
                } else {
                    console.log(currentShift);
                    if (
                        !currentShift?.shift?.id
                    ) {
                        // throw new Error("No active shift to time-out");
                    }

                    const result =
                        await timeOutShift({
                            shift_id:
                                currentShift
                                    ?.shift?.id,
                            care_worker_id:
                                currentShift
                                    ?.shift
                                    ?.care_worker_id,
                        });

                    setOnShift(false);
                    setCurrentShift(null);
                }
            } catch (err) {
                console.log(err.message);
                console.error(
                    "❌ Shift action failed:",
                    err,
                );
            }
        };

        return {
            isOnShift,
            currentShift,
            shiftEvent,
        };
    };

    const {
        isOnShift,
        currentShift,
        shiftEvent,
    } = useShiftEvent(
        location,
        currentShiftFetched,
    );

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
                                        onPress={() => {
                                            Alert.alert(
                                                "Confirm Action",
                                                `Are you sure you want to ${isOnShift ? "end" : "start"} your shift?`,
                                                [
                                                    {
                                                        text: "Cancel",
                                                        style: "cancel",
                                                    },
                                                    {
                                                        text: "Yes",
                                                        onPress:
                                                            () => {
                                                                shiftEvent(
                                                                    !isOnShift,
                                                                );
                                                                // showToastable({
                                                                //     message: `Shift ${!isOnShift ? 'started' : 'ended'} successfully!`,
                                                                //     type: 'success',
                                                                // });
                                                            },
                                                    },
                                                ],
                                                {
                                                    cancelable:
                                                        true,
                                                },
                                            );
                                        }}
                                    >
                                        {isOnShift
                                            ? "END Shift"
                                            : "START Shift"}
                                    </Button>
                                    {/* <Link
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
                                    </Link> */}
                                </XStack>
                            </YStack>
                        </XStack>
                    </YStack>
                </Card>

                <YStack>
                    {shifts &&
                    shifts.length > 0 ? (
                        <>
                            <H4
                                style={{
                                    marginBottom: 16,
                                }}
                            >
                                Scheduled
                                Visitations
                            </H4>
                            {shifts.map(
                                (item) => (
                                    <VisitationCard
                                        key={
                                            item.visitation_id
                                        }
                                        name={
                                            item.beneficiary_name
                                        }
                                        address={
                                            item.address
                                        }
                                        isArrive={
                                            item?.current_status
                                        }
                                        time={
                                            item.start_time
                                                ? new Date(
                                                      item.start_time,
                                                  ).toLocaleString(
                                                      "en-PH",
                                                      {
                                                          weekday:
                                                              "long",
                                                          year: "numeric",
                                                          month: "long",
                                                          day: "numeric",
                                                          hour: "2-digit",
                                                          minute: "2-digit",
                                                          hour12: true,
                                                          timeZone:
                                                              "Asia/Manila",
                                                      },
                                                  )
                                                : "Flexible"
                                        }
                                        type={
                                            item.visit_type ===
                                            "routine_care_visit"
                                                ? "Routine Visit"
                                                : "Other"
                                        }
                                        onArrive={() => {
                                            Alert.alert(
                                                "Confirm Arrival",
                                                "Are you sure you want to mark as arrived at this visitation?",
                                                [
                                                    {
                                                        text: "Cancel",
                                                        style: "cancel",
                                                    },
                                                    {
                                                        text: "Yes",
                                                        onPress:
                                                            () => {
                                                                if (
                                                                    currentShift
                                                                        ?.shift
                                                                        ?.care_worker_id &&
                                                                    currentShift
                                                                        ?.shift
                                                                        ?.id &&
                                                                    item?.visitation_id
                                                                ) {
                                                                    const processArrival =
                                                                        (
                                                                            loc,
                                                                        ) => {
                                                                            if (
                                                                                loc
                                                                            ) {
                                                                                ArrivedShift(
                                                                                    {
                                                                                        care_worker_id:
                                                                                            currentShift
                                                                                                .shift
                                                                                                .care_worker_id,
                                                                                        visitation_id:
                                                                                            item.visitation_id,
                                                                                        coordinates:
                                                                                            {
                                                                                                lat: loc.latitude,
                                                                                                lng: loc.longitude,
                                                                                            },
                                                                                        shift_id:
                                                                                            currentShift
                                                                                                .shift
                                                                                                .id,
                                                                                    },
                                                                                );
                                                                            } else {
                                                                                showToastable(
                                                                                    {
                                                                                        title: "Location cannot get",
                                                                                        status: "danger",
                                                                                        message:
                                                                                            "Location must be granted.",
                                                                                    },
                                                                                );
                                                                            }
                                                                        };
                                                                    if (
                                                                        !location
                                                                    ) {
                                                                        getCurrentLocation().then(
                                                                            processArrival,
                                                                        );
                                                                    } else {
                                                                        processArrival(
                                                                            location,
                                                                        );
                                                                    }
                                                                } else {
                                                                    showToastable(
                                                                        {
                                                                            title: "Start your shift first.",
                                                                            status: "danger",
                                                                            message:
                                                                                "Your shift is not started yet.",
                                                                        },
                                                                    );
                                                                }
                                                            },
                                                    },
                                                ],
                                            );
                                        }}
                                        onDepart={() => {
                                            Alert.alert(
                                                "Confirm Departure",
                                                "Are you sure you want to mark as departed from this visitation?",
                                                [
                                                    {
                                                        text: "Cancel",
                                                        style: "cancel",
                                                    },
                                                    {
                                                        text: "Yes",
                                                        onPress:
                                                            () => {
                                                                if (
                                                                    currentShift
                                                                        ?.shift
                                                                        ?.care_worker_id &&
                                                                    currentShift
                                                                        ?.shift
                                                                        ?.id &&
                                                                    item?.visitation_id
                                                                ) {
                                                                    const processDeparture =
                                                                        (
                                                                            loc,
                                                                        ) => {
                                                                            if (
                                                                                loc
                                                                            ) {
                                                                                DepartedShift(
                                                                                    {
                                                                                        care_worker_id:
                                                                                            currentShift
                                                                                                .shift
                                                                                                .care_worker_id,
                                                                                        visitation_id:
                                                                                            item.visitation_id,
                                                                                        coordinates:
                                                                                            {
                                                                                                lat: loc.latitude,
                                                                                                lng: loc.longitude,
                                                                                            },
                                                                                        shift_id:
                                                                                            currentShift
                                                                                                .shift
                                                                                                .id,
                                                                                    },
                                                                                );
                                                                            } else {
                                                                                showToastable(
                                                                                    {
                                                                                        title: "Location cannot get",
                                                                                        status: "danger",
                                                                                        message:
                                                                                            "Location must be granted.",
                                                                                    },
                                                                                );
                                                                            }
                                                                        };
                                                                    if (
                                                                        !location
                                                                    ) {
                                                                        getCurrentLocation().then(
                                                                            processDeparture,
                                                                        );
                                                                    } else {
                                                                        processDeparture(
                                                                            location,
                                                                        );
                                                                    }
                                                                } else {
                                                                    showToastable(
                                                                        {
                                                                            title: "Start your shift first.",
                                                                            status: "danger",
                                                                            message:
                                                                                "Your shift is not started yet.",
                                                                        },
                                                                    );
                                                                }
                                                            },
                                                    },
                                                ],
                                            );
                                        }}
                                    />
                                ),
                            )}
                        </>
                    ) : (
                        <YStack
                            style={{
                                flex: 1,
                                alignItems:
                                    "center",
                                justifyContent:
                                    "center",
                                padding: 16,
                            }}
                        >
                            <Text
                                style={{
                                    fontSize: 18,
                                    fontWeight:
                                        "600",
                                    marginBottom: 8,
                                }}
                            >
                                No Shifts
                                Available
                            </Text>
                            <Text
                                style={{
                                    fontSize: 14,
                                    color: "#666",
                                    textAlign:
                                        "center",
                                }}
                            >
                                You currently have
                                no scheduled
                                visitations.
                                Please check back
                                later.
                            </Text>
                        </YStack>
                    )}
                </YStack>
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
