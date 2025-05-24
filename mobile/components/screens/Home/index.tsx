import { ScrollView } from "tamagui";

import { Calendar } from "react-native-calendars";
import UserManagement from "./user-management";
import { StyleSheet } from "react-native";

const Home = () => {
    return (
       <ScrollView style={styles.container}> 
        <Calendar />
        <UserManagement />
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