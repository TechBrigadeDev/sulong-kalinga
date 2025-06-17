import os
import sys
import importlib

def fix_thinc_blis():
    """Make sure thinc can find BLIS at runtime"""
    try:
        import blis
        print(f"BLIS module found at: {os.path.dirname(blis.__file__)}")
        
        # Force reload of thinc if it's already loaded
        if 'thinc' in sys.modules:
            # Remove all thinc-related modules from sys.modules
            for key in list(sys.modules.keys()):
                if key.startswith('thinc'):
                    del sys.modules[key]
        
        # Now import thinc and test NumpyOps
        import thinc
        from thinc.api import NumpyOps
        ops = NumpyOps()
        print("Successfully initialized thinc.NumpyOps with BLIS")
        return True
    except Exception as e:
        print(f"Error in thinc_fix: {e}")
        return False

if __name__ == "__main__":
    fix_thinc_blis()