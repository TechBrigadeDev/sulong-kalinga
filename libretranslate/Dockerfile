FROM libretranslate/libretranslate:latest

# Set permissions for the directory where models are stored
USER root
RUN mkdir -p /home/libretranslate/.local/share/argos-translate/packages && \
    chmod -R 777 /home/libretranslate/.local/share

# Only load English and Tagalog (tl) models to save space and startup time
ENV LT_LOAD_ONLY=en,tl
ENV LT_UPDATE_MODELS=true
ENV LT_HOST=0.0.0.0
ENV LT_THREADS=4

# Will need network access for downloading models
EXPOSE 5000