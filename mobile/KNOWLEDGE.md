# Sulong Kalinga Mobile App - Knowledge Base

## React Native & Zustand State Management Best Practices (2025)

### State Subscription Timing Issues

**Problem**: When using Zustand stores in React components, directly accessing `store.getState()` in useEffect can lead to stale data issues, especially when the store is updated synchronously but the component hasn't re-rendered yet.

**Solution**: Use proper Zustand store subscriptions or the useStore hook with selectors to ensure components react to state changes immediately.

#### Anti-Pattern (Causes Timing Issues):
```tsx
const MyComponent = () => {
    const store = useEmergencyServiceStore();
    const request = store.getState().request; // This gets stale data
    
    useEffect(() => {
        if (request) {
            // This may not trigger when request changes
            form.reset(request);
        }
    }, [request, form]);
};
```

#### Recommended Pattern:
```tsx
const MyComponent = () => {
    const store = useEmergencyServiceStore();
    const request = store((state) => state.request); // Reactive subscription
    
    useEffect(() => {
        if (request) {
            form.reset(request);
        }
    }, [request, form]);
};
```

### Form Data Population Issues

**Common Issue**: Forms not populating on first edit attempt but working on second attempt indicates a state synchronization timing problem.

**Root Cause**: The form components are using `store.getState().request` which provides a snapshot of the current state but doesn't create a reactive subscription. When the store is updated in RequestCard's handleEdit, the form components don't re-render because they're not subscribed to state changes.

**Best Practices**:
1. Always use store selectors for reactive subscriptions
2. Avoid direct `getState()` calls in render logic
3. Use `useStore` hook with selectors for better performance
4. Consider using `store.subscribe()` for imperative operations

### Emergency Service Store Architecture

The emergency service store follows a context-based Zustand pattern with:
- Global store instance shared across components
- Context provider for dependency injection
- Custom hook for store access with error boundaries

**Store Structure**:
```tsx
interface State {
    request: IEmergencyServiceRequest | null;
    currentEmergencyServiceForm: ICurrentEmergencyServiceForm;
    // Actions
    setRequest: (request: IEmergencyServiceRequest | null) => void;
    setCurrentEmergencyServiceForm: (form: ICurrentEmergencyServiceForm) => void;
}
```

### React Hook Form Integration with Zustand

**Best Practice**: When integrating React Hook Form with Zustand, ensure state changes trigger form updates by using proper subscriptions and effect dependencies.

**Key Considerations**:
- Form reset should happen in useEffect with proper dependencies
- State subscription should be reactive, not snapshot-based
- Form validation and submission should handle both create and edit modes
- Clear form state when switching between different form types

### Performance Optimization Notes

**Zustand Selector Performance**:
- Use specific selectors to avoid unnecessary re-renders
- Prefer `store((state) => state.specificProperty)` over `store.getState()`
- Consider shallow equality for complex objects
- Use `useStore` with comparison functions for better performance

**React Native Form Performance**:
- Debounce form validation for better UX
- Use `KeyboardAvoidingView` for better mobile experience
- Implement proper loading states during form submission
- Handle form cleanup to prevent memory leaks

### Common Patterns and Solutions

#### 1. **State Synchronization Timing**
```tsx
// ❌ Wrong: Snapshot-based access
const request = store.getState().request;

// ✅ Correct: Reactive subscription
const request = store((state) => state.request);
```

#### 2. **Form State Management**
```tsx
// ✅ Proper form-store integration
useEffect(() => {
    if (request && request.type === expectedType) {
        form.reset({
            // Map request data to form fields
            field1: request.field1,
            field2: request.field2,
        });
    }
}, [request, form, expectedType]);
```

#### 3. **Store Actions with Immediate UI Updates**
```tsx
const handleEdit = () => {
    // Update store state
    store.setState({
        request: requestData,
        currentEmergencyServiceForm: requestType,
    });
    
    // Navigate or trigger UI update
    onEdit();
};
```

### Debugging State Management Issues

**Common Debugging Steps**:
1. Add console.logs to track state changes
2. Verify store provider is wrapping components correctly
3. Check useEffect dependencies and timing
4. Ensure selectors are reactive, not snapshot-based
5. Test state persistence across component re-renders

**Useful Debugging Tools**:
- React Developer Tools for component state
- Zustand DevTools for store inspection
- Console logging for state change tracking
- Network tab for API request timing

### Mobile-Specific Considerations

**React Native Performance**:
- Use FlatList for large data sets
- Implement proper keyboard handling
- Consider memory management for form state
- Test on both iOS and Android for consistency

**Expo Router Integration**:
- Handle state persistence across route changes
- Manage form state during navigation
- Clear temporary state when appropriate
- Handle deep linking with proper state initialization

## 🐛 **Emergency Service Form Population Issue - RESOLVED**

### **Issue Description**
Emergency and service assistance forms were not populating data on the first edit attempt but would work on the second attempt.

### **Root Cause Analysis**
The issue was caused by improper Zustand store subscription patterns in three key components:

1. **EmergencyAssistanceForm** (`emergency/_components/form/index.tsx`)
2. **ServiceAssistanceForm** (`service/_components/form/index.tsx`)  
3. **EmergencyServiceFormSelector** (`_components/form-selector/index.tsx`)

**Problem**: These components were using `store.getState().request` and `store((state) => state.request)` which don't work with context-based Zustand stores that return `StoreApi<State>`.

### **Technical Details**

#### **Emergency Service Store Architecture**
The emergency service store uses a **context-based Zustand pattern**:
```tsx
// Store returns StoreApi<State>, not direct hooks
export const useEmergencyServiceStore = () => {
    const store = useContext(EmergencyServiceContext);
    return store; // Returns StoreApi<State>
};
```

#### **Incorrect Pattern (Causing the Bug)**
```tsx
// ❌ This doesn't work with context-based stores
const request = store.getState().request; // Snapshot - no reactivity
const request = store((state) => state.request); // Type error - store is not callable

useEffect(() => {
    if (request) {
        form.reset(requestData); // Won't trigger on state changes
    }
}, [request, form]);
```

#### **Correct Pattern (Fixed)**
```tsx
// ✅ Proper reactive subscription for context-based stores
import { useStore } from "zustand";

const request = useStore(
    store,
    (state) => state.request,
);

useEffect(() => {
    if (request) {
        form.reset(requestData); // Triggers immediately on state changes
    }
}, [request, form]);
```

### **Solution Implementation**

#### **1. Emergency Form Fix**
```tsx
// Before
const request = store((state) => state.request);

// After  
const request = useStore(store, (state) => state.request);
```

#### **2. Service Form Fix**
```tsx
// Before
const request = store.getState().request;

// After
const request = useStore(store, (state) => state.request);
```

#### **3. Form Selector Fix**
```tsx
// Before
const currentForm = store.getState().currentEmergencyServiceForm;

// After
const currentForm = useStore(
    store,
    (state) => state.currentEmergencyServiceForm,
);
```

### **Testing the Fix**

To verify the fix works:
1. Navigate to emergency service request page
2. Create a new emergency or service request
3. From the active requests list, click "Edit" on any request
4. **Expected Result**: Form should populate immediately with request data
5. Switch between Emergency and Service tabs
6. **Expected Result**: Forms should maintain their data and populate correctly

### **Key Learnings**

#### **Context-Based vs Direct Zustand Stores**

**Direct Zustand Store (like authStore):**
```tsx
export const authStore = create<State>(() => ({...}));

// Usage - direct hook calls
const token = authStore((state) => state.token);
const { role } = authStore();
```

**Context-Based Zustand Store (like emergencyServiceStore):**
```tsx
export const useEmergencyServiceStore = () => useContext(EmergencyServiceContext);

// Usage - requires useStore wrapper
const request = useStore(store, (state) => state.request);
```

#### **When to Use Each Pattern**
- **Direct stores**: Global app state (auth, theme, etc.)
- **Context-based stores**: Feature-specific state that needs provider isolation

### **Performance Impact**
✅ **Positive**: Forms now populate immediately on first edit attempt
✅ **Positive**: Reactive subscriptions ensure real-time state updates
✅ **Positive**: Reduced user confusion and improved UX
✅ **Positive**: Consistent behavior across all form types

### **Related Components Updated**
- `features/portal/emergency-service/emergency/_components/form/index.tsx`
- `features/portal/emergency-service/service/_components/form/index.tsx`
- `features/portal/emergency-service/_components/form-selector/index.tsx`

### **Prevention Strategy**
To prevent similar issues in the future:
1. **Always use `useStore` with context-based Zustand stores**
2. **Add TypeScript strict mode to catch subscription pattern errors**
3. **Create unit tests for form population scenarios**
4. **Document store subscription patterns in component comments**

---
