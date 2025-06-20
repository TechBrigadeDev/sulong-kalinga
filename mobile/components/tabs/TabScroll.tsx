import {
    Canvas,
    LinearGradient,
    Rect,
    vec,
} from "@shopify/react-native-skia";
import {
    ArrowDown,
    ArrowUp,
} from "lucide-react-native";
import {
    RefObject,
    useRef,
    useState,
} from "react";
import {
    Animated,
    Dimensions,
    NativeScrollEvent,
    NativeSyntheticEvent,
    Pressable,
    StyleSheet,
} from "react-native";
import { GetProps, ScrollView } from "tamagui";

interface Props
    extends GetProps<typeof ScrollView> {
    showScrollUp?: boolean;
    tabbed?: boolean;
    ref?: RefObject<ScrollView | null>;
}

const TabScroll = ({
    children,
    contentContainerStyle,
    showScrollUp: _showScrollUpProp = false,
    tabbed = false,
    ref,
    ...props
}: Props) => {
    const scrollViewRef = useRef(ref || null);
    const [lastOffset, setLastOffset] =
        useState(0);
    const [_showScrollUp, setShowScrollUp] =
        useState(false);
    const [showScrollDown, setShowScrollDown] =
        useState(true);

    const { width: screenWidth } =
        Dimensions.get("window");

    const scrollTimeoutRef = useRef<ReturnType<
        typeof setTimeout
    > | null>(null);
    const scrollUpOpacity = useRef(
        new Animated.Value(0),
    ).current;
    const scrollDownOpacity = useRef(
        new Animated.Value(0),
    ).current;
    const scrollDownTranslateY = useRef(
        new Animated.Value(0),
    ).current;
    const jumpAnimation =
        useRef<Animated.CompositeAnimation | null>(
            null,
        );

    const startJumpAnimation = () => {
        if (jumpAnimation.current) {
            jumpAnimation.current.stop();
        }

        jumpAnimation.current = Animated.loop(
            Animated.sequence([
                Animated.timing(
                    scrollDownTranslateY,
                    {
                        toValue: -10,
                        duration: 600,
                        useNativeDriver: true,
                    },
                ),
                Animated.timing(
                    scrollDownTranslateY,
                    {
                        toValue: 0,
                        duration: 600,
                        useNativeDriver: true,
                    },
                ),
            ]),
        );
        jumpAnimation.current.start();
    };

    const stopJumpAnimation = () => {
        if (jumpAnimation.current) {
            jumpAnimation.current.stop();
            jumpAnimation.current = null;
        }
        Animated.timing(scrollDownTranslateY, {
            toValue: 0,
            duration: 200,
            useNativeDriver: true,
        }).start();
    };

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

        // Clear existing timeout
        if (scrollTimeoutRef.current) {
            clearTimeout(
                scrollTimeoutRef.current,
            );
        }

        // Hide scroll down indicator while scrolling
        if (
            showScrollDown &&
            scrollPercentage < 0.95
        ) {
            stopJumpAnimation();
            Animated.timing(scrollDownOpacity, {
                toValue: 0,
                duration: 150,
                useNativeDriver: true,
            }).start();
        }

        // Set timeout to show scroll down indicator when scrolling stops
        scrollTimeoutRef.current = setTimeout(
            () => {
                if (scrollPercentage < 0.95) {
                    Animated.timing(
                        scrollDownOpacity,
                        {
                            toValue: 0.5,
                            duration: 300,
                            useNativeDriver: true,
                        },
                    ).start(() => {
                        startJumpAnimation();
                    });
                }
            },
            1000,
        ); // Show after 1 second of no scrolling

        setLastOffset(contentOffset.y);

        // Handle scroll up button visibility
        if (offsetY > 100 && !_showScrollUp) {
            setShowScrollUp(true);
            Animated.timing(scrollUpOpacity, {
                toValue: 1,
                duration: 200,
                useNativeDriver: true,
            }).start();
        } else if (
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
                        bottom: tabbed ? 130 : 20,
                        zIndex: 1000,
                    },
                ]}
                pointerEvents={
                    _showScrollUp
                        ? "auto"
                        : "none"
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
                        bottom: tabbed ? 110 : 5,
                        transform: [
                            {
                                translateY:
                                    scrollDownTranslateY,
                            },
                        ],
                    },
                ]}
                pointerEvents="none"
            >
                <ArrowDown
                    color="#000"
                    size={24}
                />
            </Animated.View>

            {/* White Gradient at Bottom for Tabbed */}
            {tabbed && (
                <Canvas
                    style={styles.bottomGradient}
                    pointerEvents="none"
                >
                    <Rect
                        x={0}
                        y={0}
                        width={screenWidth}
                        height={100}
                    >
                        <LinearGradient
                            start={vec(0, 0)}
                            end={vec(0, 100)}
                            colors={[
                                "rgba(255,255,255,0)",
                                "rgba(255,255,255,1)",
                            ]}
                        />
                    </Rect>
                </Canvas>
            )}
        </>
    );
};

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
    scrollDownIndicator: {
        position: "absolute",
        left: "50%",
        marginLeft: -24,
        padding: 12,
    },
    scrollPressable: {
        justifyContent: "center",
        alignItems: "center",
    },
    bottomGradient: {
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        height: 100,
        zIndex: 1,
    },
});

export default TabScroll;
