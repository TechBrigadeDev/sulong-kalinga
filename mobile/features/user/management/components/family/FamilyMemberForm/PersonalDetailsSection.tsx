import { Card, H3, Input, Label, Text, YStack } from "tamagui";
import { IFamilyMember } from "~/user.schema";

interface Props {
    data: Partial<IFamilyMember>;
    onChange: (key: keyof IFamilyMember, value: any) => void;
}

const PersonalDetailsSection = ({ data, onChange }: Props) => {
    return (
        <Card>
            <Card.Header padded>
                <H3>Personal Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="first_name">First Name</Label>
                        <Input
                            id="first_name"
                            value={data.first_name}
                            onChangeText={(value) => onChange("first_name", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="last_name">Last Name</Label>
                        <Input
                            id="last_name"
                            value={data.last_name}
                            onChangeText={(value) => onChange("last_name", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="gender">Gender</Label>
                        <Input
                            id="gender"
                            value={data.gender}
                            onChangeText={(value) => onChange("gender", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="birthday">Birthday</Label>
                        <Input
                            id="birthday"
                            value={data.birthday}
                            onChangeText={(value) => onChange("birthday", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="mobile">Mobile Number</Label>
                        <Input
                            id="mobile"
                            value={data.mobile}
                            onChangeText={(value) => onChange("mobile", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="landline">Landline</Label>
                        <Input
                            id="landline"
                            value={data.landline}
                            onChangeText={(value) => onChange("landline", value)}
                        />
                    </YStack>
                    <YStack>
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            value={data.email}
                            onChangeText={(value) => onChange("email", value)}
                        />
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalDetailsSection;
