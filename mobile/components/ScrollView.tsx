import { useRef, useState } from "react";
import {
    NativeScrollEvent,
    NativeSyntheticEvent,
} from "react-native";
import { useSafeAreaInsets } from "react-native-safe-area-context";
import {
    ScrollView as SV,
    ScrollViewProps,
    YStack,
} from "tamagui";

interface Props extends ScrollViewProps {
    children?: React.ReactNode;
}

const ScrollView = ({
    children,
    style,
    ...props
}: Props) => {
    const insets = useSafeAreaInsets();
    const scrollViewRef = useRef<any>(null);

    const [lastOffset, setLastOffset] =
        useState(0);

    const styles = [
        {
            flex: 1,
            paddingBottom: insets.bottom,
        },
        style,
    ];

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

        setLastOffset(contentOffset.y);

        if (
            scrollPercentage >= 0.95 &&
            isScrollingDown
        ) {
            scrollViewRef.current?.scrollToEnd({
                animated: true,
            });
        }
    };

    return (
        <YStack flex={1} position="relative">
            <SV
                ref={scrollViewRef}
                flex={1}
                overflow="hidden"
                scrollEnabled={true}
                showsVerticalScrollIndicator={
                    false
                }
                showsHorizontalScrollIndicator={
                    false
                }
                style={styles}
                onScroll={handleScroll}
                scrollEventThrottle={16}
                {...props}
            >
                {children}
            </SV>

            {/* Top gradient overlay */}
            {/* <LinearGradient
                style={{
                    position: "absolute",
                    top: 0,
                    width: "100%",
                    height: 50,
                }}
                colors={[
                    "rgba(0,0,0,0.3)",
                    "transparent",
                ]}
                start={[0, 0]}
                end={[0, 1]}
            /> */}

            {/* Bottom gradient overlay */}
            {/* <LinearGradient
                style={{
                    position: "absolute",
                    bottom: 0,
                    width: "100%",
                    height: 50,
                }}
                colors={[
                    "transparent",
                    "rgba(0,0,0,0.3)",
                ]}
                start={[0, 0]}
                end={[0, 1]}
            /> */}
        </YStack>
    );
};

export default ScrollView;
