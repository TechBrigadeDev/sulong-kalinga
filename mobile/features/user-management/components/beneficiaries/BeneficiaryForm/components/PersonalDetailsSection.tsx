import {
    beneficiaryFormOpts,
    withBeneficiaryForm,
} from "features/user-management/components/beneficiaries/BeneficiaryForm/form";
import {
    Card,
    H3,
    Input,
    Text,
    XStack,
    YStack,
} from "tamagui";

const CIVIL_STATUS_OPTIONS = [
    { label: "Single", value: "Single" },
    { label: "Married", value: "Married" },
    { label: "Widowed", value: "Widowed" },
    { label: "Divorced", value: "Divorced" },
];

const GENDER_OPTIONS = [
    { label: "Male", value: "Male" },
    { label: "Female", value: "Female" },
    { label: "Other", value: "Other" },
];

// export const PersonalDetailsSection = () => {
//     return (
//         <Card elevate>
//             <Card.Header padded>
//                 <H3>Personal Details</H3>
//             </Card.Header>
//             <YStack p="$4">
//                 <YStack gap="$4">
//                     <XStack gap="$4">
//                         <FirstName />
//                         <LastName />
//                     </XStack>

//                     <XStack gap="$4">
//                         <CivilStatus />
//                         <Gender />
//                     </XStack>

//                     <XStack gap="$4">
//                         <Birthday />
//                         <PrimaryCaregiver />
//                     </XStack>

//                     <XStack gap="$4">
//                         <MobileNumber />
//                     </XStack>
//                 </YStack>
//             </YStack>
//         </Card>
//     );
// };

export const PersonalDetailsSection =
    withBeneficiaryForm({
        ...beneficiaryFormOpts,
        render: ({ form }) => (
            <Card elevate>
                <Card.Header padded>
                    <H3>Personal Details</H3>
                </Card.Header>
                <YStack p="$4">
                    <YStack gap="$4">
                        <XStack gap="$4">
                            {/* <FirstName />
                                <LastName /> */}

                            <form.AppField name="first_name">
                                {(field) => (
                                    <YStack
                                        flex={1}
                                        gap="$2"
                                    >
                                        <Text>
                                            First
                                            Name *
                                        </Text>
                                        <Input
                                            size="$4"
                                            value={
                                                field
                                                    .state
                                                    .value
                                            }
                                            onChangeText={(
                                                value,
                                            ) =>
                                                field.handleChange(
                                                    value,
                                                )
                                            }
                                            placeholder="Enter first name"
                                            autoCapitalize="words"
                                        />
                                    </YStack>
                                )}
                            </form.AppField>
                            {/* <form.AppField name="first_name">
                                {(field) => (
                                    <YStack
                                        flex={1}
                                        gap="$2"
                                    >
                                        <Text>
                                            First
                                            Name *
                                        </Text>
                                        <Input
                                            size="$4"
                                            value={
                                                field
                                                    .state
                                                    .value
                                            }
                                            onChangeText={(
                                                value,
                                            ) =>
                                                field.handleChange(
                                                    value,
                                                )
                                            }
                                            placeholder="Enter first name"
                                            autoCapitalize="words"
                                        />
                                    </YStack>
                                )}
                            </form.AppField> */}
                        </XStack>

                        <XStack gap="$4">
                            {/* <CivilStatus />
                                <Gender /> */}
                        </XStack>

                        <XStack gap="$4">
                            {/* <Birthday />
                                <PrimaryCaregiver /> */}
                        </XStack>

                        <XStack gap="$4">
                            {/* <MobileNumber /> */}
                        </XStack>
                    </YStack>
                </YStack>
            </Card>
        ),
    });

// const LastName = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="last_name">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Last Name *</Text>
//                     <Input
//                         size="$4"
//                         value={field.state.value}
//                         onChangeText={(value) =>
//                             field.handleChange(
//                                 value,
//                             )
//                         }
//                         placeholder="Enter last name"
//                         autoCapitalize="words"
//                     />
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };

// const CivilStatus = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="civil_status">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Civil Status *</Text>
//                     <Select
//                         size="$4"
//                         value={field.state.value}
//                         onValueChange={(value) =>
//                             field.handleChange(
//                                 value,
//                             )
//                         }
//                     >
//                         <Select.Trigger>
//                             <Select.Value placeholder="Select civil status" />
//                         </Select.Trigger>
//                         <Select.Content>
//                             <Select.ScrollUpButton />
//                             <Select.Viewport>
//                                 <Select.Group>
//                                     {CIVIL_STATUS_OPTIONS.map(
//                                         (
//                                             option,
//                                             i,
//                                         ) => (
//                                             <Select.Item
//                                                 index={
//                                                     i
//                                                 }
//                                                 key={
//                                                     option.value
//                                                 }
//                                                 value={
//                                                     option.value
//                                                 }
//                                             >
//                                                 <Select.ItemText>
//                                                     {
//                                                         option.label
//                                                     }
//                                                 </Select.ItemText>
//                                             </Select.Item>
//                                         ),
//                                     )}
//                                 </Select.Group>
//                             </Select.Viewport>
//                             <Select.ScrollDownButton />
//                         </Select.Content>
//                     </Select>
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };

// const Gender = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="gender">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Gender *</Text>
//                     <Select
//                         size="$4"
//                         value={field.state.value}
//                         onValueChange={(value) =>
//                             field.handleChange(
//                                 value,
//                             )
//                         }
//                     >
//                         <Select.Trigger>
//                             <Select.Value placeholder="Select gender" />
//                         </Select.Trigger>
//                         <Select.Content>
//                             <Select.ScrollUpButton />
//                             <Select.Viewport>
//                                 <Select.Group>
//                                     {GENDER_OPTIONS.map(
//                                         (
//                                             option,
//                                             i,
//                                         ) => (
//                                             <Select.Item
//                                                 index={
//                                                     i
//                                                 }
//                                                 key={
//                                                     option.value
//                                                 }
//                                                 value={
//                                                     option.value
//                                                 }
//                                             >
//                                                 <Select.ItemText>
//                                                     {
//                                                         option.label
//                                                     }
//                                                 </Select.ItemText>
//                                             </Select.Item>
//                                         ),
//                                     )}
//                                 </Select.Group>
//                             </Select.Viewport>
//                             <Select.ScrollDownButton />
//                         </Select.Content>
//                     </Select>
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };

// const Birthday = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="birthday">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Birthday *</Text>
//                     <Input
//                         size="$4"
//                         value={field.state.value}
//                         onChangeText={(value) =>
//                             field.handleChange(
//                                 value,
//                             )
//                         }
//                         placeholder="YYYY-MM-DD"
//                     />
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };

// const PrimaryCaregiver = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="primary_caregiver">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Primary Caregiver</Text>
//                     <Input
//                         size="$4"
//                         value={field.state.value}
//                         onChangeText={(value) =>
//                             field.handleChange(
//                                 value,
//                             )
//                         }
//                         placeholder="Enter Primary Caregiver name"
//                         autoCapitalize="words"
//                     />
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };

// const MobileNumber = () => {
//     const form = useBeneficiaryForm();
//     return (
//         <form.Field name="mobile">
//             {(field) => (
//                 <YStack flex={1} gap="$2">
//                     <Text>Mobile Number *</Text>
//                     <Input
//                         size="$4"
//                         value={
//                             field.state.value?.replace(
//                                 "+63",
//                                 "",
//                             ) || ""
//                         }
//                         onChangeText={(value) =>
//                             field.handleChange(
//                                 `+63${value}`,
//                             )
//                         }
//                         placeholder="Enter mobile number"
//                         keyboardType="phone-pad"
//                     />
//                 </YStack>
//             )}
//         </form.Field>
//     );
// };
