import { useUser } from '../../features/user/user.hook';
import { Tabs } from 'expo-router';
import Lucide from "@react-native-vector-icons/lucide";


export default function Layout() {
  useUser();

  return (
    <Tabs screenOptions={{ 
      tabBarActiveTintColor: "blue",
      headerShown: false,
    }}>
      <Tabs.Screen
        name="index"
        options={{
          title: "Home",
          tabBarIcon: ({ color }) => (
            <Lucide size={28} name="house" color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="options"
        options={{
          title: "Options",
          tabBarIcon: ({ color }) => (
            <Lucide size={28} name="ellipsis-vertical" color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="user-management"
        options={{
          href: null
        }}
      />
    </Tabs>
  );
}
