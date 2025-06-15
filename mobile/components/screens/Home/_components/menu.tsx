import {
    IMenuItem,
    portalMenuItems,
    staffMenuItems,
} from "components/screens/Home/paths";
import { Link } from "expo-router";
import { authStore } from "features/auth/auth.store";
import { icons } from "lucide-react-native";
import {
    StyleSheet,
    TouchableNativeFeedback,
} from "react-native";
import { Card, Text, View } from "tamagui";

const menuItems: IMenuItem[] = [
    ...portalMenuItems,
    ...staffMenuItems,
];

const HomeMenu = () => {
    return (
        <View style={menuStyle.container}>
            {menuItems.map((item, index) => (
                <MenuCard
                    key={index}
                    item={item}
                />
            ))}
        </View>
    );
};

const menuStyle = StyleSheet.create({
    container: {
        flexDirection: "row",
        flexWrap: "wrap",
        justifyContent: "space-between",
        paddingHorizontal: 16,
        paddingVertical: 16,
    },
});

const MenuCard = ({
    item,
}: {
    item: IMenuItem;
}) => {
    const { role } = authStore();
    const Icon = icons[item.icon];

    if (
        role &&
        item.permissions.length > 0 &&
        !item.permissions.includes(role)
    ) {
        return null;
    }

    return (
        <Link href={item.href} asChild>
            <TouchableNativeFeedback>
                <Card
                    style={cardStyle.card}
                    backgroundColor={item.color}
                >
                    <Icon
                        size={32}
                        color="#fff"
                        style={cardStyle.icon}
                    />
                    <Text style={cardStyle.title}>
                        {item.title}
                    </Text>
                </Card>
            </TouchableNativeFeedback>
        </Link>
    );
};

const cardStyle = StyleSheet.create({
    card: {
        height: 130,
        padding: 18,
        borderWidth: 1,
        borderColor: "#ccc",
        marginBottom: 8,
        borderRadius: 8,
        width: "48%",

        display: "flex",
        flexDirection: "column",
        justifyContent: "space-between",

        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 1,
        },
        shadowOpacity: 0.25,
        shadowRadius: 3.84,
        elevation: 2,
    },
    icon: {},
    title: {
        fontSize: 20,
        fontWeight: "bold",
        color: "#fff",
        alignSelf: "flex-end",
    },
});

export default HomeMenu;
