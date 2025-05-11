import { Card, H3, Input, Label, XStack, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";

interface Props {
    data: Partial<IBeneficiary>;
    onChange: (field: keyof IBeneficiary, value: any) => void;
}

const CognitiveFunctionSection = ({ data, onChange }: Props) => {
    return (
        <XStack space="$4" flexWrap="wrap">
            <Card elevate flex={1} minWidth={300}>
                <Card.Header padded>
                    <H3>Mobility</H3>
                </Card.Header>
                <Card.Footer padded>
                    <YStack space="$4">
                        <YStack>
                            <Label htmlFor="walking_ability">Walking Ability</Label>
                            <Input
                                id="walking_ability"
                                placeholder="Enter details about walking ability"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>

                        <YStack>
                            <Label htmlFor="assistive_devices">Assistive Devices</Label>
                            <Input
                                id="assistive_devices"
                                placeholder="Enter details about assistive devices"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>

                        <YStack>
                            <Label htmlFor="transportation_needs">Transportation Needs</Label>
                            <Input
                                id="transportation_needs"
                                placeholder="Enter details about transportation needs"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>
                    </YStack>
                </Card.Footer>
            </Card>

            <Card elevate flex={1} minWidth={300}>
                <Card.Header padded>
                    <H3>Cognitive Function</H3>
                </Card.Header>
                <Card.Footer padded>
                    <YStack space="$4">
                        <YStack>
                            <Label htmlFor="memory">Memory</Label>
                            <Input
                                id="memory"
                                placeholder="Enter details about memory"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>

                        <YStack>
                            <Label htmlFor="thinking_skills">Thinking Skills</Label>
                            <Input
                                id="thinking_skills"
                                placeholder="Enter details about thinking skills"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>

                        <YStack>
                            <Label htmlFor="orientation">Orientation</Label>
                            <Input
                                id="orientation"
                                placeholder="Enter details about orientation"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>

                        <YStack>
                            <Label htmlFor="behavior">Behavior</Label>
                            <Input
                                id="behavior"
                                placeholder="Enter details about behavior"
                                multiline
                                numberOfLines={3}
                                textAlignVertical="top"
                            />
                        </YStack>
                    </YStack>
                </Card.Footer>
            </Card>
        </XStack>
    );
};

export default CognitiveFunctionSection;
