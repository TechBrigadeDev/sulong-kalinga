import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { IVisitation } from "features/scheduling/visitation/type";
import { YStack, YStackProps } from "tamagui";

import BeneficiaryCard from "./components/BeneficiaryCard";
import CareWorkerCard from "./components/CareWorkerCard";
import LocationCard from "./components/LocationCard";
import NotesCard from "./components/NotesCard";
import StatusCard from "./components/StatusCard";

interface Props extends YStackProps {
    visitation: IVisitation;
}

const VisitationDetail = ({
    visitation,
    ..._props
}: Props) => {
    const beneficiaryFullName = `${visitation.beneficiary.first_name} ${visitation.beneficiary.last_name}`;
    const careWorkerFullName = `${visitation.care_worker.first_name} ${visitation.care_worker.last_name}`;

    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle:
                        beneficiaryFullName,
                }}
            />
            <ScrollView
                flex={1}
                style={{
                    backgroundColor: "#f9fafb",
                }}
                contentContainerStyle={{
                    paddingBlockEnd: 110,
                }}
            >
                <YStack gap="$4" p="$4">
                    <BeneficiaryCard
                        name={beneficiaryFullName}
                    />
                    <CareWorkerCard
                        name={careWorkerFullName}
                    />
                    <LocationCard
                        location={
                            visitation.beneficiary
                                .street_address
                        }
                    />
                    <StatusCard
                        beneficiaryConfirmed={
                            !!visitation.confirmed_by_beneficiary
                        }
                        familyConfirmed={
                            !!visitation.confirmed_by_family
                        }
                        confirmedOn={
                            visitation.confirmed_on
                                ? new Date(
                                      visitation.confirmed_on,
                                  ).toLocaleDateString()
                                : undefined
                        }
                    />
                    <NotesCard
                        notes={
                            visitation.notes || ""
                        }
                    />
                </YStack>
            </ScrollView>
        </>
    );
};

export default VisitationDetail;
