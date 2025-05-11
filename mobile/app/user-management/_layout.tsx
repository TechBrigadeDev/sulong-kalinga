import { Stack } from "expo-router";

const screens = [
    {
        name: "beneficiaries/edit",
        title: "Edit Beneficiary",
    },
    {
        name: "beneficiaries/view",
        title: "View Beneficiary",
    },
    {
        name: "beneficiaries/search",
        title: "Search Beneficiary",   
    },
    {
        name: "family",
        title: "Family or Relatives",
    },
    {
        name: "care-workers",
        title: "Care Workers",
    },
    {
        name: "care-managers",
        title: "Care Managers",
    },
    {
        name: "administrators",
        title: "Administrators",
    }
]

const Layout = () => {
    return (
        <Stack/>
    )
}

export default Layout;