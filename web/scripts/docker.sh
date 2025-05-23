#!/bin/bash

# Usage:
#   ./docker build -t <tag> -e <environment>
#   ./docker run <tag> <environment>

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR/.."
DOCKERFILE="$PROJECT_ROOT/docker/Dockerfile"

# Function to parse .env file and return docker env arguments
parse_env_file() {
    local env_file="$PROJECT_ROOT/.env.docker"
    local env_args=""
    
    if [ -f "$env_file" ]; then
        while IFS= read -r line || [ -n "$line" ]; do
            # Skip comments and empty lines
            if [[ $line =~ ^[^#].+=.+ ]]; then
                # Extract variable and remove any quotes
                env_args="$env_args -e ${line%%=*}=${line#*=}"
            fi
        done < "$env_file"
    fi
    echo "$env_args"
}

COMMAND=$1

if [ "$COMMAND" = "build" ]; then
    shift
    while [[ $# -gt 0 ]]; do
        case $1 in
            -t|--tag)
                TAG="$2"
                shift 2
                ;;
            -e|--env)
                ENV="$2"
                shift 2
                ;;
            *)
                echo "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    if [ -z "$TAG" ] || [ -z "$ENV" ]; then
        echo "Usage: $0 build -t <tag> -e <environment>"
        exit 1
    fi
    docker build -f "$DOCKERFILE" -t "$TAG" --build-arg ENVIRONMENT="$ENV" "$PROJECT_ROOT"
    exit $?
fi

if [ "$COMMAND" = "run" ]; then
    TAG="$2"
    ENV="$3"
    if [ -z "$TAG" ]; then
        echo "Usage: $0 run <tag> [environment]"
        exit 1
    fi

    # If environment is not test or production, parse .env file
    if [ "$ENV" != "test" ] && [ "$ENV" != "production" ]; then
        ENV_ARGS=$(parse_env_file)
        if [ -n "$ENV" ]; then
            docker run -d --name sulong-app -p 80:8000 $ENV_ARGS -e ENVIRONMENT="$ENV" "$TAG"
        else
            docker run -d --name sulong-app -p 80:8000 $ENV_ARGS "$TAG" 
        fi
    else
        # For test or production, only pass the ENVIRONMENT variable
        docker run -d --name sulong-app -p 80:8000 -e ENVIRONMENT="$ENV" "$TAG" 
    fi
    exit $?
fi

echo "Usage: $0 build -t <tag> -e <environment>"
echo "   or: $0 run <tag> [environment]"
exit 1
