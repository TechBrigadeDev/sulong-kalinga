import AvatarImage from "components/Avatar";
import FlatList from "components/FlatList";
import { useGetFamilyMembers } from "features/portal/family/hook";
import { IFamilyMember } from "features/portal/family/interface";
import {
    Mail,
    MapPin,
    Phone,
    User,
} from "lucide-react-native";
import {
    Avatar,
    Card,
    H6,
    Separator,
    Spinner,
    Text,
    XStack,
    YStack,
} from "tamagui";

const FamilyList = () => {
    const { data, isLoading } =
        useGetFamilyMembers();

    if (isLoading) {
        return (
            <Spinner
                size="large"
                style={{ margin: 20 }}
            />
        );
    }

    if (!data || data.length === 0) {
        return (
            <YStack m="$5" items="center">
                <Text>
                    No family members found.
                </Text>
            </YStack>
        );
    }

    return (
        <FlatList<IFamilyMember>
            data={data}
            renderItem={({ item }) => (
                <FamilyMemberCard member={item} />
            )}
            keyExtractor={(item) =>
                item.id.toString()
            }
            estimatedItemSize={5}
            contentContainerStyle={{
                paddingHorizontal: 16,
            }}
        />
    );
};

const FamilyMemberCard = ({
    member,
}: {
    member: IFamilyMember;
}) => {
    const fullName = `${member.first_name} ${member.last_name}`;

    const Login = () => {
        if (member.email) {
            return (
                <XStack items="center" gap="$2">
                    <Mail
                        size={16}
                        color="#4A5568"
                    />
                    <Text
                        fontSize={14}
                        color="#4A5568"
                    >
                        {member.email}
                    </Text>
                </XStack>
            );
        } else if (member.username) {
            return (
                <XStack items="center" gap="$2">
                    <User
                        size={16}
                        color="#4A5568"
                    />
                    <Text
                        fontSize={14}
                        color="#4A5568"
                    >
                        {member.username}
                    </Text>
                </XStack>
            );
        } else {
            return null;
        }
    };

    return (
        <Card
            marginBottom="$2"
            borderRadius={8}
            borderWidth={1}
            elevate
            overflow="hidden"
        >
            <XStack p="$4" gap="$2">
                <YStack gap="$1">
                    <Avatar circular size="$6">
                        <AvatarImage
                            uri={member.photo_url}
                            fallback={member.id.toString()}
                        />
                    </Avatar>
                </YStack>
                <YStack flex={1} gap="$1" p="$2">
                    <XStack
                        display="flex"
                        flexDirection="row"
                        items="center"
                        justify="space-between"
                    >
                        <H6 fontWeight="bold">
                            {fullName}
                        </H6>
                    </XStack>
                    <Separator />
                    <YStack gap="$1">
                        <Login />
                        <XStack
                            items="center"
                            gap="$2"
                        >
                            <Phone
                                size={16}
                                color="#4A5568"
                            />
                            <Text
                                fontSize={14}
                                color="#4A5568"
                            >
                                {member.mobile ||
                                    "No phone number"}
                            </Text>
                        </XStack>
                        <XStack
                            items="center"
                            gap="$2"
                        >
                            <MapPin
                                size={16}
                                color="#4A5568"
                            />
                            <Text
                                fontSize={14}
                                color="#4A5568"
                            >
                                {member.street_address ||
                                    "No address provided"}
                            </Text>
                        </XStack>
                    </YStack>
                </YStack>
            </XStack>
        </Card>
    );
};

export default FamilyList;
