import { Button, Card, H3, Input, Label, Text, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";
import { View } from "react-native";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const DocumentsSection = ({ data, onChange }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Documents and Signatures</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack space="$4">
                    {/* Beneficiary Picture */}
                    <YStack>
                        <Label>Upload Beneficiary Picture</Label>
                        <Button>Choose File</Button>
                        <Text>{data.photo ? 'Photo selected' : 'No photo chosen'}</Text>
                    </YStack>

                    {/* Review Date */}
                    <YStack>
                        <Label htmlFor="review_date">Review Date</Label>
                        <Input
                            id="review_date"
                            placeholder="MM/DD/YYYY"
                            value="05/10/2025"
                            editable={false}
                        />
                    </YStack>

                    {/* Care Service Agreement */}
                    <YStack>
                        <Label>Care Service Agreement</Label>
                        <Button>Choose File</Button>
                        <Text>
                            {data.care_service_agreement_doc ? 'File selected' : 'No file chosen'}
                        </Text>
                    </YStack>

                    {/* General Careplan */}
                    <YStack>
                        <Label>General Careplan</Label>
                        <Button>Choose File</Button>
                        <Text>
                            {data.general_care_plan_doc ? 'File selected' : 'No file chosen'}
                        </Text>
                    </YStack>

                    {/* Beneficiary Signature */}
                    <YStack>
                        <Label>Beneficiary Signature</Label>
                        <View style={{ 
                            height: 200, 
                            backgroundColor: '#f5f5f5',
                            borderRadius: 8,
                            marginVertical: 8
                        }} />
                        <Button>Clear</Button>
                    </YStack>

                    {/* Care Worker Signature */}
                    <YStack>
                        <Label>Care Worker Signature</Label>
                        <View style={{ 
                            height: 200, 
                            backgroundColor: '#f5f5f5',
                            borderRadius: 8,
                            marginVertical: 8
                        }} />
                        <Button>Clear</Button>
                    </YStack>

                    {/* Family Portal Registration */}
                    <Card bordered>
                        <Card.Header padded>
                            <Text fontSize="$5">Family Portal Account Registration</Text>
                        </Card.Header>
                        <Card.Footer padded>
                            <YStack space="$3">
                                <YStack>
                                    <Label htmlFor="portal_email">Email *</Label>
                                    <Input
                                        id="portal_email"
                                        placeholder="Enter email"
                                        keyboardType="email-address"
                                        autoCapitalize="none"
                                    />
                                </YStack>

                                <YStack>
                                    <Label htmlFor="portal_password">Password *</Label>
                                    <Input
                                        id="portal_password"
                                        placeholder="Enter password"
                                        secureTextEntry
                                    />
                                </YStack>

                                <YStack>
                                    <Label htmlFor="portal_confirm_password">Confirm Password *</Label>
                                    <Input
                                        id="portal_confirm_password"
                                        placeholder="Confirm password"
                                        secureTextEntry
                                    />
                                </YStack>
                            </YStack>
                        </Card.Footer>
                    </Card>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default DocumentsSection;
