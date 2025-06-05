import type { NativeStackHeaderProps } from "@react-navigation/native-stack";
import Constants from "expo-constants";
import { StyleSheet } from "react-native";
import { H3, View, XStack } from "tamagui";

interface Props extends Partial<NativeStackHeaderProps> {
    name: string;
}

const Header = ({ name }: Props) => {
    return (
        <View style={headerStyle.container}>
            <XStack>
                <H3 fontWeight="bold">{name}</H3>
            </XStack>
        </View>
    );
};

const headerStyle = StyleSheet.create({
    container: {
        marginTop: Constants.statusBarHeight - 5,
        paddingHorizontal: 10,
    },
    title: {
        fontWeight: "bold",
    },
});

export default Header;
