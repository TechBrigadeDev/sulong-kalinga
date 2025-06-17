import {
    KeyboardAvoidingView,
    Platform,
    StyleSheet,
} from "react-native";
import { Card, Text, View } from "tamagui";

import { AppIcon } from "~/components/Icon";

import LoginForm from "./form";

const Login = () => {
    return (
        <KeyboardAvoidingView
            behavior={
                Platform.OS === "ios"
                    ? "padding"
                    : undefined
            }
        >
            <Card
                elevate
                elevation="$3"
                maxW="75%"
                style={styles.container}
            >
                <Card.Header>
                    <Header />
                </Card.Header>
                <View>
                    <LoginForm />
                </View>
            </Card>
        </KeyboardAvoidingView>
    );
};

const Header = () => {
    return (
        <View style={styles.header}>
            <AppIcon width={100} height={100} />
            <Text style={styles.headerText}>
                Coalition of Services for the
                Elderly
            </Text>
            <Text style={styles.headerSubtitle}>
                Empowering Senior Citizens Since
                1989
            </Text>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        paddingVertical: 20,
    },
    header: {
        display: "flex",
        gap: 10,
        alignItems: "center",
        justifyContent: "center",
        paddingBottom: 15,
    },
    headerText: {
        fontSize: 20,
        textAlign: "center",
    },
    headerSubtitle: {
        fontStyle: "italic",
        fontSize: 12,
    },
});

export default Login;
