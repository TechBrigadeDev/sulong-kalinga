import { Button, Card, Input, ScrollView, Text, XStack, YStack, Checkbox } from "tamagui";
import { useState } from "react";
import { Ionicons } from "@expo/vector-icons";

export interface DiseaseTherapyIntervention {
  id: string;
  name: string;
  minutes: string;
  isCustom?: boolean;
}

const DEFAULT_INTERVENTIONS: Omit<DiseaseTherapyIntervention, "id">[] = [
  { name: "Ensure that the individual is taking medications as prescribed and understanding their purpose", minutes: "" },
  { name: "Use medication reminders, pill organizers, or caregiver assistance to help with medication adherence", minutes: "" },
  { name: "Store medications safely and out of reach to prevent accidental overdose or misuse", minutes: "" },
  { name: "Back care (light massage)", minutes: "" },
  { name: "Breathing Exercise", minutes: "" },
  { name: "Light stretching/exercise", minutes: "" },
];

export interface DiseaseTherapyData {
  interventions: DiseaseTherapyIntervention[];
}

interface DiseaseTherapyProps {
  data: DiseaseTherapyData;
  onChange: (data: Partial<DiseaseTherapyData>) => void;
}

export const DiseaseTherapy = ({ data, onChange }: DiseaseTherapyProps) => {
  const [customIntervention, setCustomIntervention] = useState("");

  const toggleIntervention = (intervention: DiseaseTherapyIntervention) => {
    const newInterventions = data.interventions.some(i => i.id === intervention.id)
      ? data.interventions.filter(i => i.id !== intervention.id)
      : [...data.interventions, intervention];
    
    onChange({ interventions: newInterventions });
  };

  const updateMinutes = (id: string, minutes: string) => {
    const newInterventions = data.interventions.map(i => 
      i.id === id ? { ...i, minutes } : i
    );
    onChange({ interventions: newInterventions });
  };

  const addCustomIntervention = () => {
    if (!customIntervention.trim()) return;
    
    const newIntervention: DiseaseTherapyIntervention = {
      id: Date.now().toString(),
      name: customIntervention,
      minutes: "",
      isCustom: true
    };
    
    onChange({ 
      interventions: [...data.interventions, newIntervention]
    });
    
    setCustomIntervention("");
  };

  const removeIntervention = (id: string) => {
    onChange({
      interventions: data.interventions.filter(i => i.id !== id)
    });
  };

  return (
    <ScrollView>
      <YStack padding="$4" gap="$4">
        <Card elevate>
          <Card.Header padded>
            <Text size="$6" fontWeight="bold">Disease/Therapy Interventions</Text>
          </Card.Header>
          <Card.Footer padded>
            <YStack gap="$4">
              {DEFAULT_INTERVENTIONS.map((intervention, index) => {
                const savedIntervention = data.interventions.find(
                  i => i.name === intervention.name
                );
                const checked = Boolean(savedIntervention);

                return (
                  <XStack key={index} gap="$4" alignItems="center">
                    <Checkbox
                      checked={checked}
                      onCheckedChange={() => 
                        toggleIntervention({
                          id: savedIntervention?.id || Date.now().toString(),
                          ...intervention
                        })
                      }
                      size="$4"
                    >
                      <Checkbox.Indicator>
                        <Ionicons name="checkmark" size={16} />
                      </Checkbox.Indicator>
                    </Checkbox>
                    
                    <YStack flex={1}>
                      <Text>{intervention.name}</Text>
                      {checked && (
                        <XStack gap="$2" marginTop="$2" alignItems="center">
                          <Input
                            flex={1}
                            placeholder="Minutes"
                            keyboardType="numeric"
                            value={savedIntervention?.minutes}
                            onChangeText={(text) => 
                              updateMinutes(savedIntervention!.id, text)
                            }
                          />
                          <Text>min</Text>
                        </XStack>
                      )}
                    </YStack>
                  </XStack>
                );
              })}

              {data.interventions
                .filter(i => i.isCustom)
                .map(intervention => (
                  <XStack key={intervention.id} gap="$4" alignItems="center">
                    <Checkbox
                      checked={true}
                      onCheckedChange={() => removeIntervention(intervention.id)}
                      size="$4"
                    >
                      <Checkbox.Indicator>
                        <Ionicons name="checkmark" size={16} />
                      </Checkbox.Indicator>
                    </Checkbox>
                    
                    <YStack flex={1}>
                      <Text>{intervention.name}</Text>
                      <XStack gap="$2" marginTop="$2" alignItems="center">
                        <Input
                          flex={1}
                          placeholder="Minutes"
                          keyboardType="numeric"
                          value={intervention.minutes}
                          onChangeText={(text) => updateMinutes(intervention.id, text)}
                        />
                        <Text>min</Text>
                      </XStack>
                    </YStack>
                    
                    <Button
                      theme="red"
                      onPress={() => removeIntervention(intervention.id)}
                      icon={<Ionicons name="trash-outline" size={16} />}
                    />
                  </XStack>
                ))
              }

              <YStack gap="$2">
                <XStack gap="$2">
                  <Input
                    flex={1}
                    placeholder="Enter custom disease/therapy intervention"
                    value={customIntervention}
                    onChangeText={setCustomIntervention}
                  />
                  <Button
                    theme="blue"
                    onPress={addCustomIntervention}
                    icon={<Ionicons name="add-outline" size={16} />}
                  >
                    Add
                  </Button>
                </XStack>
              </YStack>
            </YStack>
          </Card.Footer>
        </Card>
      </YStack>
    </ScrollView>
  );
};
