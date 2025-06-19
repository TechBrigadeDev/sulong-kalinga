FROM python:3.11-slim

WORKDIR /app

# Install minimal build dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    gcc \
    g++ \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create models directory
RUN mkdir -p /app/models && chmod 777 /app/models

# Upgrade pip with simpler command
RUN pip install --upgrade pip setuptools wheel

# Install requests first (needed for model_installer.py)
RUN pip install requests

# CRITICAL: Install exact spaCy version 3.8.1 that the model was trained with
RUN pip install spacy==3.8.1

# Now install calamancy package
RUN pip install calamancy==0.2.2

# Install other dependencies AFTER spaCy/calamancy to avoid overrides
RUN pip install \
    flask==2.0.1 \
    werkzeug==2.0.1 \
    gunicorn==20.1.0 \
    pandas==2.0.3

# Copy model installer and install the model
COPY model_installer.py ./
RUN python model_installer.py

# Copy application files
COPY *.py ./

EXPOSE 5000

CMD ["python", "-u", "app.py"]