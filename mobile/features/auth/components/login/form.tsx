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
    const { login: handleLogin, isPending } =
        useLogin({
            onSuccess: () => {
                router.replace("/(tabs)");
            },
        });

    const [login, setLogin] = useState("");
    const [password, setPassword] = useState(
        process.env.NODE_ENV !== "production"
            ? "12312312"
            : "",
    );

    const onLogin = async () => {
        await handleLogin({
            login,
            password,
        });
    };

    return (
        <View style={styles.container}>
            <Input
                placeholder="Email or Username"
                value={login}
                onChangeText={setLogin}
                style={styles.input}
            />
            <Input
                placeholder="Password"
                secureTextEntry
                value={password}
                onChangeText={setPassword}
                style={styles.input}
            />
            <Button
                onPress={onLogin}
                disabled={isPending}
                theme="accent"
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
