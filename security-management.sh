#!/bin/bash
# Script de gestion de la sécurité - Alpha Community
# Utilisation: bash security-management.sh [commande]

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "═══════════════════════════════════════════════════════════════"
echo "  GESTION DE SÉCURITÉ - ALPHA COMMUNITY"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# ===== FONCTION: Vérifier la sécurité =====
check_security() {
    echo -e "${YELLOW}[CHECK]${NC} Vérification de la sécurité..."
    php "$PROJECT_ROOT/security-check.php"
}

# ===== FONCTION: Initialiser le projet =====
init_project() {
    echo -e "${YELLOW}[INIT]${NC} Initialisation du projet..."
    
    # Créer .env depuis .env.example
    if [ ! -f "$PROJECT_ROOT/.env" ]; then
        cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
        echo -e "${GREEN}✓${NC} Fichier .env créé"
    else
        echo -e "${YELLOW}!${NC} Fichier .env existe déjà"
    fi
    
    # Créer répertoire logs
    mkdir -p "$PROJECT_ROOT/logs"
    chmod 755 "$PROJECT_ROOT/logs"
    echo -e "${GREEN}✓${NC} Répertoire logs créé"
    
    # Créer répertoire uploads
    mkdir -p "$PROJECT_ROOT/config/uploads"
    chmod 755 "$PROJECT_ROOT/config/uploads"
    echo -e "${GREEN}✓${NC} Répertoire uploads créé"
    
    # Vérifier la sécurité
    check_security
}

# ===== FONCTION: Générer une clé sécurisée =====
generate_key() {
    echo -e "${YELLOW}[KEYGEN]${NC} Génération de clé sécurisée..."
    echo ""
    
    KEY=$(php -r "echo bin2hex(random_bytes(32));")
    echo -e "${GREEN}Nouvelle clé:${NC} $KEY"
    echo ""
    echo "Ajouter dans .env:"
    echo "JWT_SECRET=$KEY"
    echo ""
}

# ===== FONCTION: Changer permissions =====
fix_permissions() {
    echo -e "${YELLOW}[PERMS]${NC} Correction des permissions..."
    
    # Répertoires
    chmod 755 "$PROJECT_ROOT/config"
    chmod 755 "$PROJECT_ROOT/includes"
    chmod 755 "$PROJECT_ROOT/php"
    chmod 755 "$PROJECT_ROOT/api"
    chmod 755 "$PROJECT_ROOT/admin"
    chmod 755 "$PROJECT_ROOT/public"
    chmod 755 "$PROJECT_ROOT/logs" 2>/dev/null || mkdir -p "$PROJECT_ROOT/logs"
    chmod 755 "$PROJECT_ROOT/config/uploads"
    
    echo -e "${GREEN}✓${NC} Permissions corrigées"
}

# ===== FONCTION: Nettoyer les logs =====
clean_logs() {
    echo -e "${YELLOW}[CLEAN]${NC} Nettoyage des logs..."
    
    DAYS=${1:-30}
    find "$PROJECT_ROOT/logs" -name "*.log" -type f -mtime +$DAYS -delete
    find "$PROJECT_ROOT/logs" -name "*.log.gz" -type f -mtime +$DAYS -delete
    
    echo -e "${GREEN}✓${NC} Logs plus anciens que $DAYS jours supprimés"
}

# ===== FONCTION: Afficher usage =====
show_usage() {
    echo "Utilisation: bash security-management.sh [commande]"
    echo ""
    echo "Commandes disponibles:"
    echo "  init              - Initialiser le projet (créer .env, dossiers, etc.)"
    echo "  check             - Vérifier la sécurité"
    echo "  keygen            - Générer une clé sécurisée"
    echo "  fix-perms         - Corriger les permissions"
    echo "  clean-logs [days] - Nettoyer les logs (par défaut: 30 jours)"
    echo "  help              - Afficher cette aide"
    echo ""
}

# ===== MAIN =====
COMMAND="${1:-help}"

case $COMMAND in
    init)
        init_project
        ;;
    check)
        check_security
        ;;
    keygen)
        generate_key
        ;;
    fix-perms)
        fix_permissions
        ;;
    clean-logs)
        clean_logs "${2:-30}"
        ;;
    help|--help|-h)
        show_usage
        ;;
    *)
        echo -e "${RED}✗ Commande inconnue: $COMMAND${NC}"
        echo ""
        show_usage
        exit 1
        ;;
esac

echo ""
