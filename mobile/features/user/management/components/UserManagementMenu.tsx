import { Card, H2, Paragraph, XStack } from "tamagui";
import { LinkProps, useRouter } from "expo-router";


const UserManagementMenu = () => {
    const links: {
        title: string;
        icon: string;
        link: LinkProps["href"];
    }[] = [
        {
            title: "Beneficiaries",
            icon: "user", 
            link: "/user-management/beneficiaries" 
        },
        { 
            title: "Family or Relatives", 
            icon: "users", 
            link: "/user-management/family" 
        },
        {
            title: "Care Workers", 
            icon: "user-md", 
            link: "/user-management/care-workers" 
        },
        { 
            title: "Care Managers", 
            icon: "user-shield", 
            link: "/user-management/care-managers" 
        },
        { 
            title: "Administrators", 
            icon: "user-cog", 
            link: "/user-management/administrators" 
        }
    ]

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
}


interface MenuCardProps {
    title: string;
    icon: string;
    link: LinkProps["href"];
}
const MenuCard = ({
    title,
    link
}: MenuCardProps) => {
    const router = useRouter();
    const handlePress = () => {
        router.push(link);
    }

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
                <H2>{title}</H2>
                <Paragraph>{link}</Paragraph>
            </Card.Header>
            <XStack>
            </XStack>
        </Card>
    );
}

export default UserManagementMenu;