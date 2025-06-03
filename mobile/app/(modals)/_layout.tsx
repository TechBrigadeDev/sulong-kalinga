import { Stack } from "expo-router"
import { StyleSheet } from "react-native"

const Layout = () => {
    return (
        <Stack>
          <Stack.Screen
            name="select-beneficiary"
            options={{
              presentation: 'modal',
            }}
          />
        </Stack>
    )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  separator: {
    marginVertical: 30,
    height: 1,
    width: '80%',
  },
});

export default Layout;