import { TouchableOpacity } from "react-native";
import { Text, XStack, YStack, styled } from "tamagui";

const StepIndicator = styled(YStack, {
  name: "StepIndicator",
  variants: {
    active: {
      true: {
        background: "$blue10",
      },
    },
    completed: {
      true: {
        background: "$green10",
      },
    },
  } as const,
});

const StepConnector = styled(YStack, {
  name: "StepConnector",
  variants: {
    completed: {
      true: {
        background: "$green10",
      },
    },
  } as const,
});

interface FormProgressProps {
  currentStep?: number;
  setStep?: (step: number) => void;
  steps?: { label: string }[];
}

export const FormProgress = ({
  currentStep = 0,
  steps = [],
  setStep
}: FormProgressProps) => {
  if (!steps || steps.length === 0) {
    return null;
  }

  const onStepClick = (index: number) => {
    if (setStep && currentStep !== index && steps[index]) {
        setStep(index);
    }
  }

  return (
    <YStack>
      <XStack>
        {steps.map((step, index) => (
          <XStack
            key={index}
            style={{
              flex: 1,
              flexDirection: "row",
              alignItems: "center",
              paddingHorizontal: 8,
              paddingVertical: 16,
            }}
          >
            <TouchableOpacity
                onPress={() => onStepClick(index)}
                style={{
                    flex: 1,
                    alignItems: "center",
                    justifyContent: "center",
                }}
            >
            <StepIndicator
              active={currentStep === index}
              completed={currentStep > index}
              style={{
                width: 30,
                height: 30,
                borderRadius: 15,
                alignItems: "center",
                justifyContent: "center",
                backgroundColor:
                  currentStep >= index
                    ? currentStep === index
                      ? "#0077ff"
                      : "#00a651"
                    : "#e0e0e0",
              }}
            >
              <Text
                style={{
                  color: currentStep >= index ? "#ffffff" : "#666666",
                  fontWeight: "bold",
                }}
              >
                {index + 1}
              </Text>
            </StepIndicator>
            </TouchableOpacity>
            {index < steps.length - 1 && (
              <StepConnector
                completed={currentStep > index}
                style={{
                  flex: 1,
                  height: 2,
                  backgroundColor:
                    currentStep > index ? "#00a651" : "#e0e0e0",
                  marginHorizontal: 4,
                }}
              />
            )}
          </XStack>
        ))}
      </XStack>
    </YStack>
  );
};
