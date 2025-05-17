import React from 'react';
import FontAwesome from '@expo/vector-icons/FontAwesome';
import { Drawer } from 'expo-router/drawer';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import Colors from '~/constants/Colors';
import { useColorScheme } from 'react-native';

function DrawerIcon(props: {
  name: React.ComponentProps<typeof FontAwesome>['name'];
  color: string;
}) {
  return <FontAwesome size={24} {...props} />;
}

export default function DrawerLayout() {
  const colorScheme = useColorScheme();

  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <Drawer
        screenOptions={{
          drawerActiveTintColor: Colors[colorScheme ?? 'light'].tint,
          headerShown: true,
        }}
      >
        <Drawer.Screen
          name="index"
          options={{
            title: 'Home',
            drawerIcon: ({ color }) => <DrawerIcon name="home" color={color} />,
          }}
        />
        <Drawer.Screen
          name="user-management"
          options={{
            title: 'User Management',
            drawerIcon: ({ color }) => <DrawerIcon name="user" color={color} />,
          }}
        />
        <Drawer.Screen
          name="options"
          options={{
            title: 'Options',
            drawerIcon: ({ color }) => <DrawerIcon name="navicon" color={color} />,
          }}
        />
      </Drawer>
    </GestureHandlerRootView>
  );
}
