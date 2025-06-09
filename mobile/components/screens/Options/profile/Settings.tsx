import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import { StyleSheet } from "react-native";
import { Avatar, Text, YStack } from "tamagui";

import Badge from "~/components/Bagde";
import UserAvatar from "~/features/user/components/UserAvatar";
import { useUserProfile } from "~/features/user/user.hook";

const ProfileSettings = () => {
    const { data: userData } = useUserProfile();

    return (
        <YStack style={styles.container}>
            <Header />
            <OptionCard style={styles.card}>
                <OptionRow
                    label="Email"
                    value={
                        userData?.email ||
                        "Not set"
                    }
                    href={
                        "/options/profile/update-email"
                    }
                />
                <OptionRow
                    label="Mobile Number"
                    value={
                        userData?.mobile ||
                        "Not set"
                    }
                />
                <OptionRow
                    label="Password"
                    href={
                        "/options/profile/update-password"
                    }
                />
            </OptionCard>
            <OptionCard style={styles.card}>
                <OptionRow
                    label="SSS ID Number"
                    value={
                        userData?.sss_id ||
                        "Not set"
                    }
                />
                <OptionRow
                    label="PhilHealth ID Number"
                    value={
                        userData?.philhealth_id ||
                        "Not set"
                    }
                />
                <OptionRow
                    label="Pag-IBIG ID Number"
                    value={
                        userData?.pagibig_id ||
                        "Not set"
                    }
                />
            </OptionCard>
        </YStack>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        paddingHorizontal: 20,
        backgroundColor: "var(--background)",
    },
    card: {
        marginBottom: 15,
    },
});

const Header = () => {
    const { data: user } = useUserProfile();

    const fullName = user
        ? `${user.first_name} ${user.last_name}`
        : "User";

    return (
        <YStack style={headerStyle.container}>
            <Avatar
                circular
                size="$8"
                marginBottom={10}
            >
                <UserAvatar />
            </Avatar>
            <Text style={headerStyle.name}>
                {fullName}
            </Text>
            <Badge
                variant={
                    user?.volunteer_status ===
                    "Active"
                        ? "success"
                        : "warning"
                }
                style={headerStyle.shadow}
                size={15}
            >
                {user?.volunteer_status}
            </Badge>
        </YStack>
    );
};

const headerStyle = StyleSheet.create({
    container: {
        padding: 20,
        alignItems: "center",
        backgroundColor: "var(--background)",
        marginBottom: 15,
    },
    name: {
        fontWeight: "bold",
        color: "#000",
        marginBottom: 10,
        fontSize: 20,
    },
    shadow: {
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 1,
        },
        shadowOpacity: 0.2,
        shadowRadius: 1.41,
    },
});

export default ProfileSettings;
