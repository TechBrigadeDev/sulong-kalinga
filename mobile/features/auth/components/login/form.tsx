import { useState } from "react";
import { useLogin } from "../../auth.hook";
import { Button, Input, View } from "tamagui";
import { StyleSheet } from "react-native";
import { useRouter } from "expo-router";

const LoginForm = () => {
  const router = useRouter();
  const { login } = useLogin();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const onLogin = async () => {
    await login({
      email,
      password,
    });
    router.replace("/(drawer)");
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
        <Button onPress={onLogin}>
            Login
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
