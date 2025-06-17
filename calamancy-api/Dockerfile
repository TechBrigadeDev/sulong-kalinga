FROM python:3.9-slim

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

# Install core dependencies in stages to avoid conflicts
# Stage 1: Scientific foundation
RUN pip install numpy==1.21.6 \
    && pip install scipy==1.7.3 \
    && pip install scikit-learn==1.0.2 \
    && pip install pandas==1.4.3

# Stage 2: NLP foundation (specific compatible versions)
RUN pip install blis==0.7.8 \
    && pip install thinc==8.1.0 \
    && pip install "spacy>=3.3.0,<3.4.0"

# Stage 3: Web API
RUN pip install flask==2.0.1 \
    && pip install werkzeug==2.0.1 \
    && pip install gunicorn==20.1.0

# Stage 4: The most important part - calamancy
RUN pip install calamancy==0.2.2

# Copy application files
COPY *.py ./

EXPOSE 5000

CMD ["python", "-u", "app.py"]