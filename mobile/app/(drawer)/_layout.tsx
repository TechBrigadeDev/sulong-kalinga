import React from 'react';
import FontAwesome from '@expo/vector-icons/FontAwesome';
import { Drawer } from 'expo-router/drawer';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import Colors from '~/constants/Colors';
import { useColorScheme } from 'react-native';
import { View, Text, StyleSheet } from 'react-native';
import { DrawerContentScrollView, DrawerItemList, DrawerItem } from '@react-navigation/drawer';
import { useUser } from '../../features/user/user.hook';
import { userStore } from '../../features/user/user.store';

function DrawerIcon(props: {
  name: React.ComponentProps<typeof FontAwesome>['name'];
  color: string;
}) {
  return <FontAwesome size={24} {...props} />;
}

function CustomDrawerContent(props: any) {
  const { user } = userStore();
  return (
    <DrawerContentScrollView {...props} contentContainerStyle={{ flex: 1 }}>
      <View style={styles.avatarContainer}>
        <View style={styles.avatarCircle}>
          <FontAwesome name="user-circle" size={64} color="#ccc" />
        </View>
        <Text style={styles.userName}>
          {user?.first_name} {user?.last_name}
        </Text>
      </View>
      <View style={{ flex: 1 }}>
        <DrawerItemList {...props} />
      </View>
      <View style={styles.bottomOptions}>
        <DrawerItem
          label="Logout"
          icon={({ color }) => <DrawerIcon name="sign-out" color={color} />}
          onPress={() => {/* TODO: Implement logout logic */}}
        />
      </View>
    </DrawerContentScrollView>
  );
}

const styles = StyleSheet.create({
  avatarContainer: {
    alignItems: 'center',
    marginTop: 32,
    marginBottom: 16,
  },
  avatarCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#eee',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 8,
  },
  userName: {
    fontWeight: 'bold',
    fontSize: 18,
  },
  bottomOptions: {
    marginBottom: 24,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: '#ccc',
    paddingTop: 8,
  },
});

export default function DrawerLayout() {
  const colorScheme = useColorScheme();
  useUser();

  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <Drawer
        drawerContent={props => <CustomDrawerContent {...props} />}
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
            drawerIcon: ({ color }) => <DrawerIcon name="cog" color={color} />, 
          }}
           
        />
      </Drawer>
    </GestureHandlerRootView>
  );
}
