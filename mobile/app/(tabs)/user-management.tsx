import { StyleSheet } from 'react-native';

import { Text, View } from '@/components/Themed';
import UserManagementMenu from '../../features/user/management/components/UserManagementMenu';

export default function TabTwoScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>User Profiles</Text>
      <UserManagementMenu/>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    fontSize: 40,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  separator: {
    marginVertical: 30,
    height: 1,
    width: '80%',
  },
});
