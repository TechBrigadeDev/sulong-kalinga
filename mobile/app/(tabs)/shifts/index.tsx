import AvatarImage from "components/Avatar";
import { Link } from "expo-router";
import { useState } from "react";
import { ScrollView } from "react-native";
import { Avatar, Button, Card, H4, Stack, Text, XStack, YStack } from "tamagui";

const VisitationCard = ({ 
  name, 
  address, 
  time, 
  type = "Regular Visit",
  onArrive,
  onDepart 
}: {
  name: string;
  address: string;
  time: string;
  type?: string;
  onArrive?: () => void;
  onDepart?: () => void;
}) => (
  <Card elevate style={{ marginBottom: 12, padding: 16 }}>
    <YStack>
      <XStack style={{ marginBottom: 8 }}>
        <Text style={{ flex: 1, fontWeight: '600' }}>{name}</Text>
        <Card 
          elevate 
          style={{ 
            backgroundColor: type === "Regular Visit" ? "#0077FF" : "#00AA00",
            borderRadius: 20,
            paddingHorizontal: 8,
            paddingVertical: 4
          }}
        >
          <Text style={{ color: "white", fontSize: 12 }}>{type}</Text>
        </Card>
      </XStack>
      <XStack style={{ marginBottom: 8, alignItems: "center", gap: 8 }}>
        <Text style={{ color: "#666" }}>📍</Text>
        <Text style={{ flex: 1, color: "#666", fontSize: 14 }}>{address}</Text>
      </XStack>
      <XStack style={{ marginBottom: 12, alignItems: "center", gap: 8 }}>
        <Text style={{ color: "#666" }}>🕒</Text>
        <Text style={{ color: "#666", fontSize: 14 }}>{time}</Text>
      </XStack>
      <XStack style={{ gap: 8 }}>
        <Button style={{ flex: 1 }} onPress={onArrive}>Arrived</Button>
        <Button style={{ flex: 1 }} onPress={onDepart}>Departed</Button>
      </XStack>
    </YStack>
  </Card>
);

const Screen = () => {
  const [isOnShift, setOnShift] = useState(true);

  return (
    <ScrollView style={{ flex: 1 }}>
      <Stack style={{ padding: 16 }}>
        <Card elevate style={{ marginBottom: 16, padding: 16 }}>
          <YStack>
            <H4 style={{ marginBottom: 16 }}>CARE WORKER ACTIVITY</H4>
            <XStack style={{ gap: 16, marginBottom: 16 }}>
              <Avatar circular size="$4">
                <AvatarImage
                    uri="https://placekitten.com/200/200"
                    fallback="CW"
                />
              </Avatar>
              <YStack style={{ flex: 1, justifyContent: "center" }}>
                <XStack style={{ alignItems: "center", gap: 8 }}>
                  <Text>Status:</Text>
                  <Card 
                    elevate 
                    style={{ 
                      backgroundColor: isOnShift ? "#00AA00" : "#666",
                      borderRadius: 20,
                      paddingHorizontal: 8,
                      paddingVertical: 4
                    }}
                  >
                    <Text style={{ color: "white", fontSize: 12 }}>
                      {isOnShift ? "On-Shift" : "Off-Shift"}
                    </Text>
                  </Card>
                </XStack>
                <XStack style={{ gap: 8, marginTop: 8 }}>
                  <Button 
                    style={{ 
                      flex: 1, 
                      backgroundColor: isOnShift ? "#FF4444" : "#00AA00" 
                    }}
                    onPress={() => setOnShift(!isOnShift)}
                  >
                    {isOnShift ? "END Shift" : "START Shift"}
                  </Button>
                  <Link href="/shifts/work-history" asChild>
                    <Button style={{ flex: 1, backgroundColor: "#0077FF" }}>
                      VIEW Work History
                    </Button>
                  </Link>
                </XStack>
              </YStack>
            </XStack>
          </YStack>
        </Card>

        <H4 style={{ marginBottom: 16 }}>Scheduled Visitations</H4>
        
        {/* Dummy data for scheduled visitations */}
        <VisitationCard
          name="John Doe"
          address="66 General Malvar Extension Barrio Jesus Dela Pena 1800, Marikina"
          time="09:00 AM"
          type="Regular Visit"
        />
        
        <VisitationCard
          name="Jane Smith"
          address="123 Sample Street, Marikina"
          time="02:00 PM"
          type="Service Request"
        />
      </Stack>
    </ScrollView>
  );
}

export default Screen;