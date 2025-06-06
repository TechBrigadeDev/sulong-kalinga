import { medicationScheduleListStore } from "features/scheduling/medication/list/store";
import { useEffect } from "react";
import { Input, InputProps } from "tamagui";

const MedicationScheduleSearch = (
    props: InputProps,
) => {
    const { search, setSearch } =
        medicationScheduleListStore();

    useEffect(() => {
        return () => {
            setSearch("");
        };
    }, [setSearch]);

    return (
        <Input
            placeholder="Search Schedule"
            value={search}
            onChangeText={(text) =>
                setSearch(text)
            }
            clearButtonMode="while-editing"
            autoCapitalize="none"
            autoCorrect={false}
            placeholderTextColor="#888"
            {...props}
        />
    );
};

export default MedicationScheduleSearch;
