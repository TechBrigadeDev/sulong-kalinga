import { View } from "tamagui"
import FamilyList from "../../../features/user/management/components/family/list"

const Family = () => {
    return (
        <View flex={1} bg="$background">
            <FamilyList />
        </View>
    )
}

export default Family;