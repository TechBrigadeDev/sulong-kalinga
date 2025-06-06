import { IFamilyMember } from "features/user-management/management.type";
import {
    Card,
    H3,
    Input,
    Label,
    YStack,
} from "tamagui";

interface Props {
    data: Partial<IFamilyMember>;
    onChange: (
        key: keyof IFamilyMember,
        value: any,
    ) => void;
}

const AddressSection = ({
    data,
    onChange,
}: Props) => {
    return (
        <Card elevate bordered>
            <Card.Header padded>
                <H3>Address Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$4">
                    <YStack gap="$2">
                        <Label htmlFor="street_address">
                            Street Address
                        </Label>
                        <Input
                            id="street_address"
                            value={
                                data.street_address
                            }
                            onChangeText={(
                                value,
                            ) =>
                                onChange(
                                    "street_address",
                                    value,
                                )
                            }
                            placeholder="Enter street address"
                            multiline
                            numberOfLines={3}
                            textAlignVertical="top"
                        />
                    </YStack>
                    <YStack gap="$2">
                        <Label htmlFor="city">
                            City/Municipality
                        </Label>
                        <Input
                            id="city"
                            value={data.city}
                            onChangeText={(
                                value,
                            ) =>
                                onChange(
                                    "city",
                                    value,
                                )
                            }
                            placeholder="Enter city"
                            autoCapitalize="words"
                        />
                    </YStack>
                    <YStack gap="$2">
                        <Label htmlFor="province">
                            Province
                        </Label>
                        <Input
                            id="province"
                            value={data.province}
                            onChangeText={(
                                value,
                            ) =>
                                onChange(
                                    "province",
                                    value,
                                )
                            }
                            placeholder="Enter province"
                            autoCapitalize="words"
                        />
                    </YStack>
                    <YStack gap="$2">
                        <Label htmlFor="postal_code">
                            Postal Code
                        </Label>
                        <Input
                            id="postal_code"
                            value={
                                data.postal_code
                            }
                            onChangeText={(
                                value,
                            ) =>
                                onChange(
                                    "postal_code",
                                    value,
                                )
                            }
                            placeholder="Enter postal code"
                            keyboardType="number-pad"
                        />
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default AddressSection;
