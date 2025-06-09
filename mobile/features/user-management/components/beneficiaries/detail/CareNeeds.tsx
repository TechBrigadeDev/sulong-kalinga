import { Card, H3, Text, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const CareNeeds = ({
    beneficiary: _beneficiary,
}: Props) => {
    const careNeeds = [
        {
            type: "Mobility",
            frequency: "dolore",
            assistance:
                "Sit quia dolorum eveniet expedita in repellat adit sunt.",
        },
        {
            type: "Cognitive / Communication",
            frequency: "magnam",
            assistance:
                "Porro consequatur est corrupti expedita et.",
        },
        {
            type: "Self-sustainability",
            frequency: "saepe",
            assistance:
                "Voluptatibus minus et fugit voluptatem.",
        },
        {
            type: "Disease / Therapy Handling",
            frequency: "omnis",
            assistance:
                "Voluptate unde quo pariatur quas sit est aut.",
        },
        {
            type: "Daily Life / Social Contact",
            frequency: "aliquam",
            assistance:
                "Eum hic natus sapiente est voluptatibus eos asperiores.",
        },
        {
            type: "Outdoor Activities",
            frequency: "excepturi",
            assistance:
                "At vero reprehenderit quia dolorem est est.",
        },
        {
            type: "Household Keeping",
            frequency: "voluptas",
            assistance:
                "Suscipit consectetur pariatur porro minus consectetur ipsa.",
        },
    ];

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Care Needs</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    {careNeeds.map(
                        (need, index) => (
                            <Card
                                key={index}
                                bordered
                            >
                                <Card.Header
                                    padded
                                >
                                    <Text fontSize="$5">
                                        {
                                            need.type
                                        }
                                    </Text>
                                </Card.Header>
                                <Card.Footer
                                    padded
                                >
                                    <YStack gap="$2">
                                        <Text
                                            opacity={
                                                0.6
                                            }
                                        >
                                            Frequency:{" "}
                                            {
                                                need.frequency
                                            }
                                        </Text>
                                        <Text>
                                            {
                                                need.assistance
                                            }
                                        </Text>
                                    </YStack>
                                </Card.Footer>
                            </Card>
                        ),
                    )}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default CareNeeds;
