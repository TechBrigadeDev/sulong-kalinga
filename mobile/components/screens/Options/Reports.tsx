import { isStaff } from "features/auth/auth.util";
import React from "react";

import OptionCard from "./_components/Card";
import { Link } from "./_components/Link";
import Section from "./_components/Section";
import Title from "./_components/Title";

const Reports = () => {
    if (!isStaff()) return null;
    return (
        <Section>
            <Title name="Reports" />
            <OptionCard>
                <Link
                    label="Care Records"
                    href="/options/reports/care-records"
                    icon="FileText"
                />
            </OptionCard>
        </Section>
    );
};

export default Reports;
