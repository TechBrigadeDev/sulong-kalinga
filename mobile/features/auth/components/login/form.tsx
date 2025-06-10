import { useRouter } from "expo-router";
import { useLogin } from "features/auth/auth.hook";
import { useState } from "react";
import { StyleSheet } from "react-native";
import {
    Button,
    Input,
    Spinner,
    View,
} from "tamagui";

const LoginForm = () => {
    const router = useRouter();
    const { login, isPending } = useLogin();

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    const onLogin = async () => {
        await login({
            email,
            password,
        });
        router.replace("/(tabs)");
    };

    return (
        <View style={styles.container}>
            <Input
                placeholder="Email"
                value={email}
                onChangeText={setEmail}
                style={styles.input}
            />
            <Input
                placeholder="Password"
                secureTextEntry
                onChangeText={setPassword}
                style={styles.input}
            />
            <Button
                onPress={onLogin}
                disabled={isPending}
            >
                {isPending ? (
                    <Spinner size="small" />
                ) : (
                    "Login"
                )}
            </Button>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        paddingHorizontal: 20,
        display: "flex",
        gap: 10,
    },
    input: {
        borderWidth: 1,
        borderColor: "black",
        padding: 10,
        width: "100%",
    },
});

export default LoginForm;
