import { Stack, useRouter } from "expo-router";
import { ICareWorker } from "~/features/user/user.schema";
import CareWorkerForm from "../../../../../features/user/management/components/care-workers/form";


const Screen = () => {
  const router = useRouter();

  const handleSubmit = (data: Partial<ICareWorker>) => {
    console.log("Submitting beneficiary data:", data);
    // TODO: Add API call to create beneficiary
    router.back();
  };

  return (
    <>
      <Stack.Screen
        options={{
          headerShown: true,
          title: "Add Care Worker",
        }}
      />
      <CareWorkerForm />
    </>
  );
};

export default Screen;
