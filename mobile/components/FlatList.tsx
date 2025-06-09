import MaskedView from "@react-native-masked-view/masked-view";
import {
    FlashList,
    FlashListProps,
} from "@shopify/flash-list";
import { LinearGradient } from "expo-linear-gradient";
import { ArrowUp } from "lucide-react-native";
import { useRef, useState } from "react";
import {
    Animated,
    Pressable,
    StyleSheet,
    View,
} from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";

type FlatListProps<T> = Omit<
    FlashListProps<T>,
    "data" | "renderItem"
> & {
    data: T[];
    renderItem: ({
        item,
    }: {
        item: T;
    }) => React.ReactElement;
    estimatedItemSize?: number;
    tabbed?: boolean;
};

function FlatList<T>({
    data,
    renderItem,
    estimatedItemSize = 100,
    contentContainerStyle,
    tabbed = false,
    ...props
}: FlatListProps<T>) {
    const insets = useSafeAreaInsets();
    const listRef = useRef<FlashList<T>>(null);

    const [showScrollUp, setShowScrollUp] =
        useState(false);

    const scrollUpOpacity = useRef(
        new Animated.Value(0),
    ).current;

    const handleScroll = (event: any) => {
        const offsetY =
            event.nativeEvent.contentOffset.y;

        if (offsetY > 100 && !showScrollUp) {
            setShowScrollUp(true);
            Animated.timing(scrollUpOpacity, {
                toValue: 1,
                duration: 200,
                useNativeDriver: true,
            }).start();
        } else if (
            offsetY <= 100 &&
            showScrollUp
        ) {
            setShowScrollUp(false);
            Animated.timing(scrollUpOpacity, {
                toValue: 0,
                duration: 200,
                useNativeDriver: true,
            }).start();
        }
    };

    const scrollToTop = () => {
        listRef.current?.scrollToOffset({
            offset: 0,
            animated: true,
        });
    };

    return (
        <MaskedView
            style={{ flex: 1 }}
            maskElement={
                <View style={{ flex: 1 }}>
                    <LinearGradient
                        colors={[
                            "transparent",
                            "#000000",
                            "#000000",
                            "transparent",
                        ]}
                        locations={[
                            0, 0.05, 0.9, 1,
                        ]}
                        style={
                            StyleSheet.absoluteFill
                        }
                        start={{ x: 0, y: 0 }}
                        end={{ x: 0, y: 0.95 }}
                    />
                </View>
            }
        >
            <FlashList
                ref={listRef}
                {...props}
                data={data}
                renderItem={renderItem}
                estimatedItemSize={
                    estimatedItemSize
                }
                showsVerticalScrollIndicator={
                    false
                }
                onScroll={handleScroll}
                contentContainerStyle={{
                    paddingVertical: 24,
                    paddingBottom: 120,
                    ...(contentContainerStyle as any),
                }}
            />
            <Animated.View
                style={[
                    styles.scrollUpButton,
                    {
                        bottom: tabbed ? 75 : 0,
                        opacity: scrollUpOpacity,
                    },
                ]}
                pointerEvents={
                    showScrollUp ? "auto" : "none"
                }
            >
                <Pressable
                    onPress={scrollToTop}
                    style={
                        styles.scrollUpPressable
                    }
                >
                    <ScrollUp />
                </Pressable>
            </Animated.View>
        </MaskedView>
    );
}

const ScrollUp = () => (
    <ArrowUp color="#fff" size={24} />
);

const styles = StyleSheet.create({
    scrollUpButton: {
        position: "absolute",
        right: 20,
        backgroundColor: "#000",
        borderRadius: 30,
        padding: 12,
        elevation: 5,
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 2,
        },
        shadowOpacity: 0.25,
        shadowRadius: 3.84,
    },
    scrollUpPressable: {
        justifyContent: "center",
        alignItems: "center",
    },
});

export default FlatList;
