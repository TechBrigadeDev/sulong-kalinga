import {
    IGroupedMedicationSchedule,
    IMedicationSchedule,
} from "./medication.type";

export const groupMedicationScheduleByBeneficiary =
    (
        schedules: IMedicationSchedule[],
    ): IGroupedMedicationSchedule[] => {
        const grouped: Record<
            number,
            IGroupedMedicationSchedule
        > = {};

        schedules.forEach((schedule) => {
            const beneficiaryId =
                schedule.beneficiary_id;

            if (!grouped[beneficiaryId]) {
                grouped[beneficiaryId] = {
                    beneficiary:
                        schedule.beneficiary,
                    medication_schedules: [],
                };
            }

            grouped[
                beneficiaryId
            ].medication_schedules.push(schedule);
        });

        // sort by beneficiary's first name
        return Object.values(grouped).sort(
            (a, b) =>
                a.beneficiary.first_name.localeCompare(
                    b.beneficiary.first_name,
                ),
        );
    };
