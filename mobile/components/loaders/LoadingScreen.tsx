import {
    SafeAreaView,
    StyleSheet,
} from "react-native";
import { Spinner } from "tamagui";

const LoadingScreen = () => (
    <SafeAreaView style={styles.container}>
        <Spinner size="large" color="$color" />
    </SafeAreaView>
);

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
    },
});

export default LoadingScreen;
