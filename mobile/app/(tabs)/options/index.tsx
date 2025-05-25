import { Link as ExpoLink, LinkProps, useRouter } from "expo-router";
import { StyleSheet, TouchableOpacity } from "react-native";
import { Card as TCard, ScrollView, Text, View, XStack, YStack, H3 } from "tamagui";
import { icons } from "lucide-react-native";
import Constants from "expo-constants";
import Header from "~/components/Header";
import LogoutButton from "../../../features/auth/components/logout/button";

const Screen = () => {
    return (
        <View>
            <Header name="Options"/>
            <ScrollView style={style.scroll}>
                <UserManagement/>
                <LogoutButton/>
            </ScrollView>
        </View>
    )
}

const UserManagement = () => {
    return (
        <Section>
            <Title name="User Management"/>
            <Card>
                <Link
                    label="Beneficiaries"
                    href="/options/user-management/beneficiaries"
                    icon="HandHelping"
                />
                <Link
                    label="Families"
                    href="/options/user-management/family"
                    icon="UsersRound"
                />
                <Link
                    label="Care Workers"
                    href="/options/user-management/care-workers"
                    icon="HeartHandshake"
                />
                <Link
                    label="Care Managers"
                    href="/options/user-management/care-managers"
                    icon="Smile"
                />
            </Card>
        </Section>
    )
}

const Card = ({
    children
}: {
    children?: React.ReactNode;
}) => {
    return (
        <TCard style={style.sectionCard}>
            {children}
        </TCard>
    )
}

const Section = ({
    children,
}:{
    children: React.ReactNode;
}) => {
    return (
        <YStack style={style.section}>
            {children}
        </YStack>
    )
}

const Title = ({
    name: title
}: {
    name: string;
}) => <Text style={style.sectionTitle}>
    {title}
</Text>

const Link = ({
    href,
    label,
    icon
}:{
    href: LinkProps["href"];
    label: string;
    icon: keyof typeof icons;
}) => {
    const router = useRouter();
    const Icon = icons[icon];
    const Chevron = icons.ChevronRight;

    const handlePress = () => {
        console.log(`Navigating to ${href}`);
        router.push(href);
    }

    return (
            <TouchableOpacity 
                style={style.link}
                onPressIn={handlePress}
            >
                <XStack gap={10} style={style.linkLabel} onPress={handlePress}>
                    <Icon size={24} color="#000" />
                    <Text>
                        {label}
                    </Text>
                </XStack>
                <Chevron size={24} color="#000" style={{ marginLeft: 'auto' }} />
            </TouchableOpacity>
    )
}



const style = StyleSheet.create({
    scroll: {
        marginTop: 20,
        paddingHorizontal: 20,
    },
    section: {
        paddingHorizontal: 20,
    },
    sectionTitle: {
        fontSize: 15,
        fontWeight: "bold",
        marginBottom: 10,
    },
    sectionCard: {
        backgroundColor: "#fff",
        paddingHorizontal: 20,
    },
    link: {
        display: "flex",
        flexDirection: "row",
        gap: 10,
        paddingVertical: 15,
    },
    linkLabel: {
        alignItems: "center",
    }
});

export default Screen;