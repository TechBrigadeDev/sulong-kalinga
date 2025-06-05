import Calendar from "components/Calendar";
import { SafeAreaView } from "react-native";
import { Text } from "tamagui";


const Screen = () => {
    return (
        <SafeAreaView>
            <Calendar/>
            <Text>
                Scheduling
            </Text>
        </SafeAreaView>
    );
}

export default Screen;