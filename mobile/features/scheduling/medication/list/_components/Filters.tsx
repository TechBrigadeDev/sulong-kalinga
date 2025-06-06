import { medicationScheduleListStore } from "features/scheduling/medication/list/store";
import { IMedicationScheduleStatus } from "features/scheduling/medication/medication.type";
import {
    Check,
    ChevronDown,
    ChevronUp,
} from "lucide-react-native";
import { useMemo } from "react";
import {
    Adapt,
    Select,
    Sheet,
    XStack,
    YStack,
} from "tamagui";
import { LinearGradient } from "tamagui/linear-gradient";

const MedicationListFilters = () => {
    return (
        <XStack mx="$4">
            <Status />
        </XStack>
    );
};

const Status = () => {
    const { status, setStatus } =
        medicationScheduleListStore();

    const onValueChange = (
        val: string | null,
    ) => {
        setStatus(
            val === "all"
                ? null
                : (val as IMedicationScheduleStatus) ||
                      "active",
        );
    };

    const StatusItems = useMemo<
        (IMedicationScheduleStatus | "all")[]
    >(
        () => [
            "all",
            "active",
            "paused",
            "completed",
            "discontinued",
        ],
        [],
    );

    return (
        <Select
            value={status as string}
            onValueChange={onValueChange}
            disablePreventBodyScroll
            // {...props}
        >
            <Select.Trigger
                maxWidth={220}
                iconAfter={ChevronDown}
            >
                <Select.Value placeholder="Status" />
            </Select.Trigger>

            <Adapt when="maxMd" platform="touch">
                <Sheet
                    modal
                    dismissOnSnapToBottom
                    animation="medium"
                >
                    <Sheet.Frame>
                        <Sheet.ScrollView>
                            <Adapt.Contents />
                        </Sheet.ScrollView>
                    </Sheet.Frame>
                    <Sheet.Overlay
                        bg="$shadowColor"
                        animation="lazy"
                        enterStyle={{
                            opacity: 0,
                        }}
                        exitStyle={{
                            opacity: 0,
                        }}
                    />
                </Sheet>
            </Adapt>

            <Select.Content zIndex={200000}>
                <Select.ScrollUpButton
                    items="center"
                    content="center"
                    position="relative"
                    width="100%"
                    height="$3"
                >
                    <YStack z={10}>
                        <ChevronUp size={20} />
                    </YStack>
                    <LinearGradient
                        start={[0, 0]}
                        end={[0, 1]}
                        fullscreen
                        colors={[
                            "$background",
                            "transparent",
                        ]}
                        rounded="$4"
                    />
                </Select.ScrollUpButton>

                <Select.Viewport
                    animation="quick"
                    animateOnly={[
                        "transform",
                        "opacity",
                    ]}
                    // enterStyle={{ o: 0, y: -10 }}
                    // exitStyle={{ o: 0, y: 10 }}
                    minW={200}
                >
                    <Select.Group>
                        <Select.Label>
                            Status
                        </Select.Label>
                        {StatusItems.map(
                            (item, i) => {
                                return (
                                    <Select.Item
                                        index={i}
                                        key={item}
                                        value={
                                            item
                                        }
                                    >
                                        <Select.ItemText>
                                            {item.toLocaleUpperCase()}
                                        </Select.ItemText>
                                        <Select.ItemIndicator marginLeft="auto">
                                            <Check
                                                size={
                                                    16
                                                }
                                            />
                                        </Select.ItemIndicator>
                                    </Select.Item>
                                );
                            },
                        )}
                    </Select.Group>
                </Select.Viewport>

                <Select.ScrollDownButton
                    items="center"
                    content="center"
                    position="relative"
                    width="100%"
                    height="$3"
                >
                    <YStack z={10}>
                        <ChevronDown size={20} />
                    </YStack>
                    <LinearGradient
                        start={[0, 0]}
                        end={[0, 1]}
                        fullscreen
                        colors={[
                            "transparent",
                            "$background",
                        ]}
                        rounded="$4"
                    />
                </Select.ScrollDownButton>
            </Select.Content>
        </Select>
    );
};

export default MedicationListFilters;
