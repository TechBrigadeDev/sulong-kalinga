import os
import sys
import importlib

def patch_numpy():
    """Create a complete _core module for numpy with multiarray support"""
    try:
        # Make sure numpy is imported safely
        if 'numpy' not in sys.modules:
            import numpy
        else:
            numpy = sys.modules['numpy']
        
        print(f"Working with NumPy version: {numpy.__version__}")
        
        numpy_path = os.path.dirname(numpy.__file__)
        core_path = os.path.join(numpy_path, '_core')
        
        # Check if _core exists and create it if needed
        if not os.path.exists(core_path):
            print("numpy._core not found, creating compatibility module")
            os.makedirs(core_path, exist_ok=True)
            
            # Create an empty __init__.py file inside _core
            with open(os.path.join(core_path, '__init__.py'), 'w') as f:
                f.write('# Compatibility module created by patch\n')
                f.write('from numpy import *\n')
        else:
            print("numpy._core exists")
            
        # ALWAYS create/update multiarray.py
        multiarray_path = os.path.join(core_path, 'multiarray.py')
        with open(multiarray_path, 'w') as f:
            f.write('# Mock multiarray module\n')
            f.write('import numpy as np\n')
            f.write('from numpy import ndarray, dtype, array\n\n')
            f.write('# Common functions needed by scikit-learn\n')
            f.write('def zeros(shape, dtype=float):\n')
            f.write('    return np.zeros(shape, dtype=dtype)\n\n')
            f.write('def ones(shape, dtype=float):\n')
            f.write('    return np.ones(shape, dtype=dtype)\n\n')
            f.write('def array_equal(a, b):\n')
            f.write('    return np.array_equal(a, b)\n')
        
        # Also create a umath.py file which is often needed
        umath_path = os.path.join(core_path, 'umath.py')
        with open(umath_path, 'w') as f:
            f.write('# Mock umath module\n')
            f.write('from numpy import *\n')
        
        # Create an empty _multiarray_umath.py file
        mm_path = os.path.join(core_path, '_multiarray_umath.py')
        with open(mm_path, 'w') as f:
            f.write('# Mock _multiarray_umath module\n')
            f.write('from numpy import *\n')
        
        print(f"Updated numpy._core modules at {core_path}")
        
        # CRITICAL: Force Python to recognize the new modules
        for key in list(sys.modules.keys()):
            if key.startswith('numpy._core'):
                del sys.modules[key]
                
        # Force a reload of numpy to pick up changes
        importlib.reload(numpy)
                
        # Make the _core directory a proper package
        sys.path.insert(0, numpy_path)
        
        # Verify it works by trying to import
        try:
            import numpy._core.multiarray
            print("Successfully verified numpy._core.multiarray can be imported")
            return True
        except ImportError as e:
            print(f"Warning: Can't import numpy._core.multiarray: {e}")
            return False
            
    except Exception as e:
        print(f"Error in patch_numpy: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == "__main__":
    patch_numpy()