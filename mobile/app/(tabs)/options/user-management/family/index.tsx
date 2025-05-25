import { View } from "tamagui"
import FamilyList from "~/features/user/management/components/family/list";
import Header from "~/components/Header";
import { StyleSheet } from "react-native";

const Family = () => {
    return (
        <View flex={1} bg="$background">
            <Header name="Family Profiles" />
            <View style={style.container}>
                <FamilyList />
            </View>
        </View>
    )
}

const style = StyleSheet.create({
    container: {
        marginHorizontal: 30
    }
})

export default Family;