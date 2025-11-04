#!/bin/bash

# Script para importar todas las traducciones del módulo Admin Notifications
# Uso: bash import-all.sh

echo "========================================="
echo "Admin Notifications - Importar Traducciones"
echo "========================================="
echo ""

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Directorio base del módulo
MODULE_DIR="modules/custom/admin_notifications/translations"

# Array de idiomas y sus archivos
declare -A LANGUAGES=(
    ["es"]="Español"
    ["en"]="English"
    ["fr"]="Français"
    ["pt-br"]="Português (Brasil)"
    ["ja"]="日本語"
)

echo "Este script importará las traducciones para los siguientes idiomas:"
for lang_code in "${!LANGUAGES[@]}"; do
    echo "  - ${LANGUAGES[$lang_code]} ($lang_code)"
done
echo ""

read -p "¿Desea continuar? (s/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[SsYy]$ ]]
then
    echo "Operación cancelada."
    exit 1
fi

echo ""

# Función para importar un idioma
import_language() {
    local lang_code=$1
    local lang_name=$2

    echo -e "${BLUE}Procesando ${lang_name} (${lang_code})...${NC}"

    # Verificar si el idioma ya existe
    if drush language:info $lang_code &> /dev/null; then
        echo "  → Idioma ya existe"
    else
        echo "  → Agregando idioma..."
        drush language:add $lang_code -y
    fi

    # Importar traducciones
    echo "  → Importando traducciones..."
    drush locale:import $lang_code $MODULE_DIR/$lang_code.po --type=customized --override=all -y

    echo -e "${GREEN}  ✓ ${lang_name} importado correctamente${NC}"
    echo ""
}

# Importar cada idioma
for lang_code in "${!LANGUAGES[@]}"; do
    import_language "$lang_code" "${LANGUAGES[$lang_code]}"
done

# Limpiar caché
echo -e "${BLUE}Limpiando caché...${NC}"
drush cr

echo ""
echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}¡Todas las traducciones se han importado!${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo "Para cambiar el idioma del sitio:"
echo "  1. Ve a Configuración → Regional e idioma → Idiomas"
echo "  2. Selecciona el idioma predeterminado"
echo ""
echo "O usa Drush:"
echo "  drush config:set system.site default_langcode es -y"
echo ""
