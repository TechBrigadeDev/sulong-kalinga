import { authStore } from "features/auth/auth.store";
import { isStaff } from "features/auth/auth.util";
import React from "react";

import OptionCard from "./_components/Card";
import { Link } from "./_components/Link";
import Section from "./_components/Section";
import Title from "./_components/Title";

const UserManagement = () => {
    const { role } = authStore();
    if (!isStaff() || !role) return null;

    return (
        <Section>
            <Title name="User Management" />
            <OptionCard>
                <Link
                    label="Beneficiaries"
                    href="/options/user-management/beneficiaries"
                    icon="HandHelping"
                />
                <Link
                    label="Families"
                    href="/options/user-management/family"
                    icon="UsersRound"
                />
                {role !== "care_worker" && (
                    <Link
                        label="Care Workers"
                        href="/options/user-management/care-workers"
                        icon="HeartHandshake"
                    />
                )}
                {role === "admin" && (
                    <>
                        <Link
                            label="Care Managers"
                            href="/options/user-management/care-managers"
                            icon="Smile"
                        />
                        <Link
                            label="Administrators"
                            href="/options/user-management/admins"
                            icon="ShieldUser"
                        />
                    </>
                )}
            </OptionCard>
        </Section>
    );
};

export default UserManagement;
