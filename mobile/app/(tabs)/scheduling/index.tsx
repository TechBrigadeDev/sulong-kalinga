import { SafeAreaView } from "react-native";
import { Text, View } from "tamagui";
import { Calendar } from "react-native-calendars";


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