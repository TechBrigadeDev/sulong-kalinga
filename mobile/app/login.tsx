import { Button, TextInput } from "react-native";
import { View } from "../components/Themed";
import { useState } from "react";
import { useLogin } from "../features/auth/auth.hook";

const LoginScreen = () => {
    const {
        login
    } = useLogin();

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    const onLogin = async () => {
        await login({
            email,
            password
        });
    }

    return (
        <View>
            <TextInput
                placeholder="Email"
                value={email}
                onChangeText={setEmail}
                style={{
                    borderWidth: 1,
                    borderColor: "black",
                    padding: 10,
                    marginTop: 10,
                    width: "100%",
                }}
            />
            <TextInput
                placeholder="Password"
                secureTextEntry
                onChangeText={setPassword}
                style={{
                    borderWidth: 1,
                    borderColor: "black",
                    padding: 10,
                    marginTop: 10,
                    width: "100%",
                }}
            />
            <Button 
                title="Login"
                onPress={onLogin}
            />
        </View>
    )
}

export default LoginScreen;