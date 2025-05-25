import { ScrollView } from "tamagui";

import { StyleSheet } from "react-native";
import Profile from "./profile";

const Home = () => {
    return (
        <ScrollView style={styles.container}> 
            <Profile/>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: {
    },
    calendar: {
        marginBottom: 20,
    },
});

export default Home;