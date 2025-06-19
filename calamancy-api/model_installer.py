import os
import subprocess
import requests
import sys
import warnings
from importlib.metadata import distributions

def download_and_install_calamancy_model():
    """Download, rename and install the Calamancy model with proper versioning."""
    print("Setting up Calamancy model...")
    
    # Verify spaCy version
    try:
        import spacy
        print(f"spaCy version: {spacy.__version__}")
    except ImportError:
        print("spaCy not found")
    
    # Verify calamancy package is installed
    try:
        import calamancy
        print(f"Calamancy package found (version: {calamancy.__version__})")
    except ImportError:
        print("Calamancy package not found")
        return False
    
    # Create models directory if it doesn't exist
    os.makedirs("models", exist_ok=True)
    
    # Define model URLs and filenames
    model_url = "https://huggingface.co/ljvmiranda921/tl_calamancy_md/resolve/main/tl_calamancy_md-any-py3-none-any.whl"
    download_path = os.path.join("models", "tl_calamancy_md-original.whl")
    renamed_path = os.path.join("models", "tl_calamancy_md-0.2.0-py3-none-any.whl")
    
    # Check if we already have the renamed wheel
    if os.path.exists(renamed_path):
        print(f"Using cached model at {renamed_path}")
    else:
        # Download the wheel
        print(f"Downloading model from {model_url}...")
        try:
            response = requests.get(model_url, stream=True)
            response.raise_for_status()
            
            with open(download_path, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    f.write(chunk)
            
            # Rename to valid wheel filename
            os.rename(download_path, renamed_path)
            print(f"Downloaded and renamed model to {renamed_path}")
        except Exception as e:
            print(f"Error downloading model: {e}")
            return False
    
    # Install the renamed wheel
    print("Installing model...")
    try:
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            subprocess.check_call([sys.executable, "-m", "pip", "install", renamed_path, "--force-reinstall"])
        print("Model installation complete!")
    except Exception as e:
        print(f"Error installing model: {e}")
        return False
    
    # Alternative loading approach
    print("Attempting to load the model with calamancy.load...")
    try:
        import calamancy
        nlp = calamancy.load("tl_calamancy_md")
        print("Test load successful!")
        doc = nlp("Ito ay isang test.")
        print("Model works correctly!")
        print(f"Entities found: {[(ent.text, ent.label_) for ent in doc.ents]}")
    except Exception as e:
        print(f"Error loading model: {e}")
        # Try to load with a different approach
        try:
            import spacy
            print("Attempting direct spaCy loading...")
            nlp = spacy.load("tl_calamancy_md")
            print("Direct spaCy load successful!")
        except Exception as e2:
            print(f"Error with direct loading: {e2}")
            return False
    
    return True

if __name__ == "__main__":
    download_and_install_calamancy_model()