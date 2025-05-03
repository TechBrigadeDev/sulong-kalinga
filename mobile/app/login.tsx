import { Button, TextInput } from "react-native";
import { Text, View } from "../components/Themed";
import { useState } from "react";
import { useLogin } from "../features/auth/auth.hook";

const LoginScreen = () => {
    const {
        login
    } = useLogin();

    const [name, setName] = useState("John Doe");

    const onLogin = async () => {
        await login(name, "password");
    }

    return (
        <View>
            <Text>Login Screen</Text>
            <Button 
                title="Login"
                onPress={onLogin}
            />
            <TextInput
                placeholder="Name"
                value={name}
                onChangeText={setName}
                style={{
                    borderWidth: 1,
                    borderColor: "black",
                    padding: 10,
                    marginTop: 10,
                    width: "100%",
                }}
            />
        </View>
    )
}

export default LoginScreen;