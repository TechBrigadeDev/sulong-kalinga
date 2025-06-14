import { ToSignature } from "features/app/debug/toSignature";
import { StyleSheet } from "react-native";

import TabScroll from "~/components/tabs/TabScroll";

import HomeMenu from "./menu";
import Profile from "./profile";

const Home = () => {
    return (
        <TabScroll style={styles.container}>
            <Profile />
            <HomeMenu />
            <ToSignature />
        </TabScroll>
    );
};

const styles = StyleSheet.create({
    container: {},
    calendar: {
        marginBottom: 20,
    },
});

export default Home;
