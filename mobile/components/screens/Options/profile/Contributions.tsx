import OptionCard from "components/screens/Options/_components/Card";
import OptionRow from "components/screens/Options/_components/Row";
import { useUserProfile } from "features/user/user.hook";

import { profileStyles } from "./styles";

const Contributions = () => {
    const { staffData } = useUserProfile();
    if (!staffData) return null;

    return (
        <OptionCard style={profileStyles.card}>
            <OptionRow
                label="SSS ID Number"
                value={
                    staffData?.sss_id || "Not set"
                }
            />
            <OptionRow
                label="PhilHealth ID Number"
                value={
                    staffData?.philhealth_id ||
                    "Not set"
                }
            />
            <OptionRow
                label="Pag-IBIG ID Number"
                value={
                    staffData?.pagibig_id ||
                    "Not set"
                }
            />
        </OptionCard>
    );
};

export default Contributions;
