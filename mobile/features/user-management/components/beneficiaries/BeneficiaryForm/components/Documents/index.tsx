import {
    Card,
    H3,
    XStack,
    YStack,
} from "tamagui";

import { BeneficiaryPhoto } from "./BeneficiaryPhoto";
import { BeneficiarySignature } from "./BeneficiarySignature";
import { CareServiceAgreement } from "./CareServiceAgreement";
import { CareWorkerSignature } from "./CareWorkerSignature";
import { GeneralCarePlan } from "./GeneralCarePlan";
import { ReviewDate } from "./ReviewDate";

export const DocumentsSection = () => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Documents and Signatures</H3>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <BeneficiaryPhoto />
                <ReviewDate />
                <CareServiceAgreement />
                <GeneralCarePlan />

                <XStack gap="$4">
                    <BeneficiarySignature />
                    <CareWorkerSignature />
                </XStack>
            </YStack>
        </Card>
    );
};
