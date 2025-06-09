import { IVisitation } from "features/scheduling/visitation/type";
import { YStackProps } from "tamagui";

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
    ...props
}: Props) => {
    const beneficiaryFullName = `${visitation.beneficiary.first_name} ${visitation.beneficiary.last_name}`;
    const careWorkerFullName = `${visitation.care_worker.first_name} ${visitation.care_worker.last_name}`;

    return (
        <>
            <BeneficiaryCard
                name={beneficiaryFullName}
            />
            <CareWorkerCard
                name={careWorkerFullName}
            />
            {/* <VisitDetailsCard
                date={new Date(
                    visitation.visitation_date,
                ).toLocaleDateString()}
                time={
                    visitation.is_flexible_time
                        ? "Flexible Time"
                        : `${new Date(visitation.start_time ?? "").toLocaleTimeString()} - ${new Date(visitation.end_time ?? "").toLocaleTimeString()}`
                }
                type={visitation.visit_type}
                status={visitation.status}
            /> */}
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
                notes={visitation.notes || ""}
            />
        </>
    );
};

export default VisitationDetail;
