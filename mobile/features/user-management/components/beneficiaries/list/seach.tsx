import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { beneficiaryListStore } from "./store";

const BeneficiariesSearch = () => {
    const { setSearch } = beneficiaryListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Beneficiary"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default BeneficiariesSearch;
