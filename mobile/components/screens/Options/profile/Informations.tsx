import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import { useUserProfile } from "features/user/user.hook";
import { useCallback, useMemo } from "react";

import { profileStyles } from "./styles";

const Information = () => {
    const {
        data: userData,
        isStaff,
        staffData,
    } = useUserProfile();

    const Age = useCallback(() => {
        if (isStaff) return null;

        const age = userData?.birthday
            ? new Date().getFullYear() -
              new Date(
                  userData.birthday,
              ).getFullYear()
            : null;

        return (
            <OptionRow
                label="Age"
                value={
                    age !== null
                        ? age.toString()
                        : "Not set"
                }
            />
        );
    }, [isStaff, userData]);

    const civilStatus = useMemo(() => {
        if (userData?.role === "family_member")
            return "N/A";

        return (
            userData?.civil_status || "Not set"
        );
    }, [userData]);

    const EducationalBackground =
        useCallback(() => {
            if (!isStaff) return null;

            return (
                <OptionRow
                    label="Education"
                    value={
                        staffData?.educational_background ||
                        "Not set"
                    }
                />
            );
        }, [isStaff, staffData]);

    const PrimaryCaregiver = useCallback(() => {
        if (userData?.role !== "beneficiary")
            return null;

        return (
            <OptionRow
                label="Primary Caregiver"
                value={
                    userData?.primary_caregiver ||
                    "Not set"
                }
            />
        );
    }, [userData]);

    const Religion = useCallback(() => {
        if (!isStaff) return null;

        return (
            <OptionRow
                label="Religion"
                value={
                    staffData?.religion ||
                    "Not set"
                }
            />
        );
    }, [isStaff, staffData]);

    return (
        <OptionCard style={profileStyles.card}>
            <OptionRow
                label="Date of Birth"
                value={
                    userData?.birthday
                        ? new Date(
                              userData.birthday,
                          ).toLocaleDateString()
                        : "Not set"
                }
            />
            <OptionRow
                label="Gender"
                value={
                    userData?.gender || "Not set"
                }
            />
            <Age />

            <OptionRow
                label="Civil Status"
                value={civilStatus}
            />

            <EducationalBackground />
            <Religion />
            <PrimaryCaregiver />
        </OptionCard>
    );
};

export default Information;
