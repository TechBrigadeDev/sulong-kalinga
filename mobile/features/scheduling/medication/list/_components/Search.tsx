import { medicationScheduleListStore } from "features/scheduling/medication/list/store";
import {
    Input,
    InputProps,
    useDebounce,
} from "tamagui";

const MedicationScheduleSearch = (
    props: InputProps,
) => {
    const { search, setSearch } =
        medicationScheduleListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Schedule"
            defaultValue={search}
            onChangeText={onSearch}
            clearButtonMode="while-editing"
            autoCapitalize="none"
            autoCorrect={false}
            placeholderTextColor="#888"
            {...props}
        />
    );
};

export default MedicationScheduleSearch;
