import {
    ArrowDown,
    ArrowUp,
} from "lucide-react-native";
import { useRef, useState } from "react";
import {
    Animated,
    NativeScrollEvent,
    NativeSyntheticEvent,
    Pressable,
    StyleSheet,
} from "react-native";
import { GetProps, ScrollView } from "tamagui";

interface Props
    extends GetProps<typeof ScrollView> {
    showScrollUp?: boolean;
}

const TabScroll = ({
    children,
    contentContainerStyle,
    showScrollUp = false,
    ...props
}: Props) => {
    const scrollViewRef = useRef<any>(null);
    const [lastOffset, setLastOffset] =
        useState(0);
    const [_showScrollUp, setShowScrollUp] =
        useState(false);
    const [showScrollDown, setShowScrollDown] =
        useState(true);

    const scrollUpOpacity = useRef(
        new Animated.Value(0),
    ).current;
    const scrollDownOpacity = useRef(
        new Animated.Value(0.5),
    ).current;

    const handleScroll = (
        event: NativeSyntheticEvent<NativeScrollEvent>,
    ) => {
        const {
            layoutMeasurement,
            contentOffset,
            contentSize,
        } = event.nativeEvent;
        const scrollPercentage =
            (contentOffset.y +
                layoutMeasurement.height) /
            contentSize.height;
        const isScrollingDown =
            contentOffset.y > lastOffset;
        const offsetY = contentOffset.y;

        setLastOffset(contentOffset.y);

        // Handle scroll up button visibility
        if (offsetY > 100 && !showScrollUp) {
            setShowScrollUp(true);
            Animated.timing(scrollUpOpacity, {
                toValue: 1,
                duration: 200,
                useNativeDriver: true,
            }).start();
        } else if (
            showScrollUp &&
            offsetY <= 100 &&
            _showScrollUp &&
            !isScrollingDown
        ) {
            setShowScrollUp(false);
            Animated.timing(scrollUpOpacity, {
                toValue: 0,
                duration: 200,
                useNativeDriver: true,
            }).start();
        }

        // Handle scroll down indicator visibility
        if (scrollPercentage >= 0.95) {
            setShowScrollDown(false);
            Animated.timing(scrollDownOpacity, {
                toValue: 0,
                duration: 200,
                useNativeDriver: true,
            }).start();
        } else if (
            scrollPercentage < 0.95 &&
            !showScrollDown
        ) {
            setShowScrollDown(true);
            Animated.timing(scrollDownOpacity, {
                toValue: 0.5,
                duration: 200,
                useNativeDriver: true,
            }).start();
        }

        // Auto-scroll to end when near bottom
        if (
            scrollPercentage >= 0.95 &&
            isScrollingDown
        ) {
            scrollViewRef.current?.scrollToEnd({
                animated: false,
            });
        }
    };

    const scrollToTop = () => {
        scrollViewRef.current?.scrollTo({
            y: 0,
            animated: true,
        });
    };

    return (
        <>
            <ScrollView
                ref={scrollViewRef}
                onScroll={handleScroll}
                scrollEventThrottle={16}
                contentContainerStyle={{
                    paddingBlockEnd: 110,
                    ...(contentContainerStyle as any),
                }}
                {...props}
            >
                {children}
            </ScrollView>

            {/* Scroll Up Button */}
            <Animated.View
                style={[
                    styles.scrollUpButton,
                    {
                        opacity: scrollUpOpacity,
                    },
                ]}
                pointerEvents={
                    showScrollUp ? "auto" : "none"
                }
            >
                <Pressable
                    onPress={scrollToTop}
                    style={styles.scrollPressable}
                >
                    <ArrowUp
                        color="#fff"
                        size={24}
                    />
                </Pressable>
            </Animated.View>

            {/* Scroll Down Indicator */}
            <Animated.View
                style={[
                    styles.scrollDownIndicator,
                    {
                        opacity:
                            scrollDownOpacity,
                    },
                ]}
                pointerEvents="none"
            >
                <ArrowDown
                    color="#000"
                    size={24}
                />
            </Animated.View>
        </>
    );
};

const styles = StyleSheet.create({
    scrollUpButton: {
        position: "absolute",
        right: 20,
        bottom: 20,
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
    scrollDownIndicator: {
        position: "absolute",
        left: "50%",
        marginLeft: -24,
        bottom: 5,
        padding: 12,
    },
    scrollPressable: {
        justifyContent: "center",
        alignItems: "center",
    },
});

export default TabScroll;
