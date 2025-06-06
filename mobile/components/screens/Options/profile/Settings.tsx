import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import { StyleSheet } from "react-native";
import { Avatar, Text, YStack } from "tamagui";

import Badge from "~/components/Bagde";
import UserAvatar from "~/features/user/components/UserAvatar";
import { useUser } from "~/features/user/user.hook";

const ProfileSettings = () => {
    const { data: userData } = useUser();

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
                    value={"1234567890"}
                />
                <OptionRow
                    label="PhilHealth ID Number"
                    value={"1234567890"}
                />
                <OptionRow
                    label="Pag-IBIG ID Number"
                    value={"1234567890"}
                />
            </OptionCard>
            {/* <YStack gap="$3">
          <XStack gap="$2">
            <Text 
            value={"1234567890"}
ontWeight="bold">Email:</Text>
            <Text>{userData?.email}</Text>
          </XStack>
          <XStack gap="$2" style={{ alignItems: 'center' }}>
            <Text 
            value={"1234567890"}
ontWeight="bold">Account Status:</Text>
            <Text style={{ background: '#22c55e', color: 'white', padding: '2px 8px', borderRadius: 4, fontSize: 13 }}>
              <Badge variant={userData?.status === 'Active' ? 'success' : 'warning'}>
                {userData?.status}
              </Badge>
            </Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">SSS ID Number:</Text>
            <Text>1234567890</Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">PhilHealth ID Number:</Text>
            <Text>123456789011</Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">Pag-IBIG ID Number:</Text>
            <Text>123456789000</Text>
          </XStack>
        </YStack>
        <XStack gap="$3" style={{ marginTop: 32, justifyContent: 'flex-end' }}>
          <Button theme="blue" onPress={handleUpdateEmail}>Update Email</Button>
          <Button theme="blue" onPress={handleUpdatePassword}>Update Password</Button>
        </XStack> */}
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
    const { data: user } = useUser();

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
                    user?.status === "Active"
                        ? "success"
                        : "warning"
                }
                style={headerStyle.shadow}
                size={15}
            >
                {user?.status}
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
