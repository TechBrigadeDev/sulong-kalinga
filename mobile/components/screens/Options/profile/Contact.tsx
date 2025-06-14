import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import { useUserProfile } from "features/user/user.hook";
import { useCallback, useMemo } from "react";

import { profileStyles } from "./styles";

const Contact = () => {
    const {
        data: userData,
        isStaff,
        staffData,
    } = useUserProfile();

    const address = useMemo(() => {
        if (isStaff) {
            return staffData?.address;
        } else if (
            userData?.role === "family_member"
        ) {
            return userData.street_address;
        } else if (
            userData?.role === "beneficiary"
        ) {
            return userData?.street_address;
        }
        return "Not set";
    }, [isStaff, staffData, userData]);

    const EmergencyContact = useCallback(() => {
        let contactValue = null;

        if (userData?.role === "beneficiary") {
            contactValue =
                userData.emergency_contact_name;
        }

        if (!contactValue) return null;
        return (
            <OptionRow
                label="Emergency Contact"
                value={contactValue || "Not set"}
            />
        );
    }, [userData]);

    const EmergencyMobile = useCallback(() => {
        let contactValue = null;

        if (userData?.role === "beneficiary") {
            contactValue =
                userData.emergency_contact_mobile;
        }

        console.log(userData?.role, contactValue);

        if (!contactValue) return null;
        return (
            <OptionRow
                label="Emergency Mobile"
                value={contactValue || "Not set"}
            />
        );
    }, [userData]);

    const EmergencyEmail = useCallback(() => {
        let contactValue = null;

        if (userData?.role === "beneficiary") {
            contactValue =
                userData.emergency_contact_mobile;
        }

        if (!contactValue) return null;
        return (
            <OptionRow
                label="Emergency Email"
                value={contactValue || "Not set"}
            />
        );
    }, [userData]);

    return (
        <OptionCard style={profileStyles.card}>
            <OptionRow
                label="Mobile"
                value={
                    userData?.mobile || "Not set"
                }
            />
            <OptionRow
                label="Landline"
                value={
                    userData?.landline ||
                    "Not set"
                }
            />
            <OptionRow
                label="Address"
                value={address || "Not set"}
            />
            <EmergencyContact />
            <EmergencyMobile />
            <EmergencyEmail />
        </OptionCard>
    );
};

export default Contact;
