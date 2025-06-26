#!/bin/bash

# NLP Services Management Script
# Usage:
#   ./nlp.sh build [calamancy-api|libretranslate|both]
#   ./nlp.sh deploy [calamancy-api|libretranslate|both] [test|prod]
#   ./nlp.sh logs [calamancy-api|libretranslate]
#   ./nlp.sh stop [calamancy-api|libretranslate|both]

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

build_service() {
    local service=$1
    local tag=${2:-"local"}
    
    case $service in
        calamancy-api)
            log "Building calamancy-api service..."
            docker build -f "$PROJECT_ROOT/calamancy-api/Dockerfile" \
                -t "sulong-kalinga-calamancy-api:$tag" \
                "$PROJECT_ROOT/calamancy-api"
            ;;
        libretranslate)
            log "Building libretranslate service..."
            docker build -f "$PROJECT_ROOT/libretranslate/Dockerfile" \
                -t "sulong-kalinga-libretranslate:$tag" \
                "$PROJECT_ROOT/libretranslate"
            ;;
        both)
            build_service calamancy-api "$tag"
            build_service libretranslate "$tag"
            ;;
        *)
            error "Unknown service: $service"
            return 1
            ;;
    esac
}

deploy_local() {
    local service=$1
    local tag=${2:-"local"}
    
    case $service in
        calamancy-api)
            log "Deploying calamancy-api locally..."
            docker stop sulong-calamancy-api 2>/dev/null || true
            docker rm sulong-calamancy-api 2>/dev/null || true
            docker run -d --name sulong-calamancy-api -p 5000:5000 \
                -e PYTHONUNBUFFERED=1 \
                -e OPENAI_API_KEY="${OPENAI_API_KEY}" \
                "sulong-kalinga-calamancy-api:$tag"
            ;;
        libretranslate)
            log "Deploying libretranslate locally..."
            docker stop sulong-libretranslate 2>/dev/null || true
            docker rm sulong-libretranslate 2>/dev/null || true
            docker run -d --name sulong-libretranslate -p 5051:5000 \
                -e LT_LOAD_ONLY=en,tl \
                -e LT_UPDATE_MODELS=true \
                -e LT_HOST=0.0.0.0 \
                -e LT_THREADS=4 \
                "sulong-kalinga-libretranslate:$tag"
            ;;
        both)
            deploy_local calamancy-api "$tag"
            deploy_local libretranslate "$tag"
            ;;
        *)
            error "Unknown service: $service"
            return 1
            ;;
    esac
}

trigger_github_deploy() {
    local service=$1
    local environment=$2
    
    info "Triggering GitHub deployment for $service to $environment..."
    
    # This requires GitHub CLI (gh) to be installed and authenticated
    if ! command -v gh &> /dev/null; then
        error "GitHub CLI (gh) is not installed. Please install it to trigger remote deployments."
        return 1
    fi
    
    gh workflow run deploy.nlp.yml \
        -f service="$service" \
        -f environment="$environment"
    
    log "GitHub deployment workflow triggered for $service to $environment"
}

show_logs() {
    local service=$1
    
    case $service in
        calamancy-api)
            docker logs -f sulong-calamancy-api
            ;;
        libretranslate)
            docker logs -f sulong-libretranslate
            ;;
        *)
            error "Unknown service: $service"
            return 1
            ;;
    esac
}

stop_service() {
    local service=$1
    
    case $service in
        calamancy-api)
            log "Stopping calamancy-api..."
            docker stop sulong-calamancy-api 2>/dev/null || true
            docker rm sulong-calamancy-api 2>/dev/null || true
            ;;
        libretranslate)
            log "Stopping libretranslate..."
            docker stop sulong-libretranslate 2>/dev/null || true
            docker rm sulong-libretranslate 2>/dev/null || true
            ;;
        both)
            stop_service calamancy-api
            stop_service libretranslate
            ;;
        *)
            error "Unknown service: $service"
            return 1
            ;;
    esac
}

show_help() {
    echo "NLP Services Management Script"
    echo ""
    echo "Usage:"
    echo "  $0 build [calamancy-api|libretranslate|both] [tag]"
    echo "  $0 deploy [calamancy-api|libretranslate|both] [test|prod|local] [tag]"
    echo "  $0 logs [calamancy-api|libretranslate]"
    echo "  $0 stop [calamancy-api|libretranslate|both]"
    echo "  $0 help"
    echo ""
    echo "Examples:"
    echo "  $0 build both                    # Build both services with 'local' tag"
    echo "  $0 build calamancy-api v1.0     # Build calamancy-api with 'v1.0' tag"
    echo "  $0 deploy both local            # Deploy both services locally"
    echo "  $0 deploy calamancy-api test    # Trigger GitHub deployment to test"
    echo "  $0 logs calamancy-api           # Show logs for calamancy-api"
    echo "  $0 stop both                    # Stop both services"
}

# Main script logic
COMMAND=$1
SERVICE=$2
ENVIRONMENT_OR_TAG=$3
TAG=$4

case $COMMAND in
    build)
        if [ -z "$SERVICE" ]; then
            error "Service name required"
            show_help
            exit 1
        fi
        build_service "$SERVICE" "$ENVIRONMENT_OR_TAG"
        ;;
    deploy)
        if [ -z "$SERVICE" ]; then
            error "Service name required"
            show_help
            exit 1
        fi
        
        ENV=${ENVIRONMENT_OR_TAG:-"local"}
        
        if [ "$ENV" = "local" ]; then
            deploy_local "$SERVICE" "$TAG"
        else
            trigger_github_deploy "$SERVICE" "$ENV"
        fi
        ;;
    logs)
        if [ -z "$SERVICE" ]; then
            error "Service name required"
            show_help
            exit 1
        fi
        show_logs "$SERVICE"
        ;;
    stop)
        if [ -z "$SERVICE" ]; then
            error "Service name required"
            show_help
            exit 1
        fi
        stop_service "$SERVICE"
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        error "Unknown command: $COMMAND"
        show_help
        exit 1
        ;;
esac