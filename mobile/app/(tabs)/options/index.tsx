import { StyleSheet } from "react-native";
import { ScrollView } from "tamagui";

const Screen = () => {
    return (
        <ScrollView style={style.container}>
        </ScrollView>
    )
}

const style = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#000',
    },
});

export default Screen;