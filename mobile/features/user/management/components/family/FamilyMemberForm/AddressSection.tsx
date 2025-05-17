import { Card, H3, Input, Label, Text, YStack } from "tamagui";
import { IFamilyMember } from "~/user.schema";

interface Props {
    data: Partial<IFamilyMember>;
    onChange: (key: keyof IFamilyMember, value: any) => void;
}

const AddressSection = ({ data, onChange }: Props) => {
    return (
        <Card>
            <Card.Header padded>
                <H3>Address Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    <YStack>
                        <Label htmlFor="street_address">Street Address</Label>
                        <Input
                            id="street_address"
                            value={data.street_address}
                            onChangeText={(value) => onChange("street_address", value)}
                        />
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default AddressSection;
