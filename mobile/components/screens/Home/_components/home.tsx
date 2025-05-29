import { ScrollView } from "tamagui";

import { StyleSheet } from "react-native";
import Profile from "./profile";
import HomeMenu from "./menu";

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