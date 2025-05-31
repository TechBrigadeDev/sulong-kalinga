import { SafeAreaView } from "react-native";
import { Calendar } from "react-native-calendars";
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