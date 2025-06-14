import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import {
    hasRole,
    roleLabel,
} from "features/auth/auth.util";
import { useCallback, useMemo } from "react";
import { StyleSheet } from "react-native";
import { Avatar, Text, YStack } from "tamagui";

import Badge from "~/components/Bagde";
import UserAvatar from "~/features/user/components/UserAvatar";
import { useUserProfile } from "~/features/user/user.hook";

import Contributions from "./Contributions";
import Information from "./Informations";
import Contact from "./Contact";

const ProfileSettings = () => {
    const {
        data: userData,
        isStaff,
        staffData,
    } = useUserProfile();

    const login = useMemo(() => {
        if (!userData) return "Not set";

        if (userData.role === "beneficiary")
            return userData.username;

        return userData.email;
    }, [userData]);

    const Role = useCallback(() => {
        if (!isStaff || !staffData?.role)
            return null;

        return (
            <OptionRow
                label="Role"
                value={
                    roleLabel(staffData?.role) ||
                    "Not set"
                }
            />
        );
    }, [isStaff, staffData]);
    return (
        <YStack style={styles.container}>
            <Header />
            <OptionCard style={styles.card}>
                <Role />
                <OptionRow
                    label={
                        hasRole("beneficiary")
                            ? "Username"
                            : "Email"
                    }
                    value={login || "Not set"}
                    {...(!hasRole(
                        "beneficiary",
                    ) && {
                        href: "/options/profile/update-email",
                    })}
                />
                <OptionRow
                    label="Password"
                    href={
                        "/options/profile/update-password"
                    }
                />
            </OptionCard>
            <Information />
            <Contact />
            <Contributions />
        </YStack>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        paddingBlockEnd: 30,
    },
    card: {
        marginBottom: 15,
    },
});

const Header = () => {
    const {
        data: user,
        staffData,
        isStaff,
    } = useUserProfile();

    const fullName = user
        ? `${user.first_name} ${user.last_name}`
        : "User";

    const Status = () => {
        if (!staffData) {
            return null;
        }

        return (
            <Badge
                variant={
                    staffData?.volunteer_status ===
                    "Active"
                        ? "success"
                        : "warning"
                }
                style={headerStyle.shadow}
                size={15}
            >
                {staffData?.volunteer_status}
            </Badge>
        );
    };

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
            <Status />
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
