import { useRef, useState } from "react";
import { NativeScrollEvent, NativeSyntheticEvent } from "react-native";
import { GetProps, ScrollView } from "tamagui";

const TabScroll = ({
    children,
    contentContainerStyle,
    ...props
}: GetProps<typeof ScrollView>) => {
    const scrollViewRef = useRef<any>(null);
    const [lastOffset, setLastOffset] = useState(0);

    const handleScroll = (event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const { layoutMeasurement, contentOffset, contentSize } = event.nativeEvent;
        const scrollPercentage = (contentOffset.y + layoutMeasurement.height) / contentSize.height;
        const isScrollingDown = contentOffset.y > lastOffset;
        
        setLastOffset(contentOffset.y);

        if (scrollPercentage >= 0.99 && isScrollingDown) {
            scrollViewRef.current?.scrollToEnd({ animated: false });
        }
    };

    return (
        <ScrollView
            ref={scrollViewRef}
            onScroll={handleScroll}
            scrollEventThrottle={16}
            contentContainerStyle={{
                paddingBlockEnd: 110,
                ...(contentContainerStyle as any)
            }}
            {...props}
        >
            {children}
        </ScrollView>
    )
}

export default TabScroll;