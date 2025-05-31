import { StyleSheet } from "react-native";
import { ScrollView } from "tamagui";

import HomeMenu from "./menu";
import Profile from "./profile";

const Home = () => {
    return (
        <ScrollView style={styles.container}> 
            <Profile/>
            <HomeMenu/>
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