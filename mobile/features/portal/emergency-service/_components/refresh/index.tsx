import { RotateCcw } from "lucide-react-native";
import { useRef } from "react";
import {
    Animated,
    TouchableWithoutFeedback,
} from "react-native";
import { View } from "tamagui";

interface Props {
    onPress: () => void;
}

const RefreshButton = ({ onPress }: Props) => {
    const rotateAnim = useRef(
        new Animated.Value(0),
    ).current;

    const handlePressIn = () => {
        Animated.timing(rotateAnim, {
            toValue: 1,
            duration: 200,
            useNativeDriver: true,
        }).start(() => {
            // Trigger onPress when rotation is complete
            onPress();
            // Automatically rotate back
            Animated.timing(rotateAnim, {
                toValue: 0,
                duration: 200,
                useNativeDriver: true,
            }).start();
        });
    };

    const rotate = rotateAnim.interpolate({
        inputRange: [0, 1],
        outputRange: ["0deg", "-90deg"],
    });

    return (
        <View>
            <TouchableWithoutFeedback
                onPressIn={handlePressIn}
            >
                <Animated.View
                    style={{
                        transform: [{ rotate }],
                    }}
                >
                    <RotateCcw
                        size={24}
                        color="white"
                    />
                </Animated.View>
            </TouchableWithoutFeedback>
        </View>
    );
};

export default RefreshButton;
