import {
    LinkProps,
    useRouter,
} from "expo-router";
import { Card, H3, XStack } from "tamagui";

const UserManagementMenu = () => {
    const links: {
        title: string;
        icon: string;
        link: LinkProps["href"];
    }[] = [
        {
            title: "Beneficiaries",
            icon: "user",
            link: "/(tabs)/options/user-management/beneficiaries",
        },
        {
            title: "Family or Relatives",
            icon: "users",
            link: "/(tabs)/options/user-management/family",
        },
        {
            title: "Care Workers",
            icon: "user-md",
            link: "/(tabs)/options/user-management/care-workers",
        },
        {
            title: "Care Managers",
            icon: "user-shield",
            link: "/(tabs)/options/user-management/care-managers",
        },
        {
            title: "Administrators",
            icon: "user-cog",
            link: "/(tabs)/options/user-management/admins",
        },
    ];

    return (
        <XStack
            $maxMd={{ flexDirection: "column" }}
            gap={20}
        >
            {links.map((link, index) => (
                <MenuCard
                    key={index}
                    title={link.title}
                    icon={link.icon}
                    link={link.link}
                />
            ))}
        </XStack>
    );
};

interface MenuCardProps {
    title: string;
    icon: string;
    link: LinkProps["href"];
}
const MenuCard = ({
    title,
    link,
}: MenuCardProps) => {
    const router = useRouter();
    const handlePress = () => {
        router.push(link);
    };

    return (
        <Card
            elevate
            animation="bouncy"
            scale={0.9}
            hoverStyle={{
                scale: 1.05,
            }}
            onPressIn={handlePress}
        >
            <Card.Header padded>
                <H3>{title}</H3>
            </Card.Header>
        </Card>
    );
};

export default UserManagementMenu;
