import type { NativeStackHeaderProps } from "@react-navigation/native-stack";
import Constants from "expo-constants";
import { H3, View, XStack } from "tamagui";

interface Props
    extends Partial<NativeStackHeaderProps> {
    name: string;
    headerRight?: () => React.ReactNode;
}

const Header = ({ name, headerRight }: Props) => {
    return (
        <View mt={Constants.statusBarHeight}>
            <XStack
                justify="space-between"
                items="center"
                px={"$4"}
                py={"$2"}
            >
                <H3 fontWeight="bold">{name}</H3>
                {headerRight && headerRight()}
            </XStack>
        </View>
    );
};

export default Header;
