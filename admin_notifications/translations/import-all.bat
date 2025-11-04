@echo off
REM Script para importar todas las traducciones del módulo Admin Notifications (Windows)
REM Uso: import-all.bat

echo =========================================
echo Admin Notifications - Importar Traducciones
echo =========================================
echo.

echo Este script importara las traducciones para los siguientes idiomas:
echo   - Español (es)
echo   - English (en)
echo   - Français (fr)
echo   - Português Brasil (pt-br)
echo   - 日本語 (ja)
echo.

set /p CONFIRM="¿Desea continuar? (s/n): "
if /i not "%CONFIRM%"=="s" (
    echo Operacion cancelada.
    exit /b
)

echo.
echo =========================================
echo Procesando idiomas...
echo =========================================
echo.

REM Español
echo [1/5] Procesando Español (es)...
drush language:add es -y 2>nul
drush locale:import es modules/custom/admin_notifications/translations/es.po --type=customized --override=all -y
echo   OK Español importado
echo.

REM Inglés
echo [2/5] Procesando English (en)...
drush language:add en -y 2>nul
drush locale:import en modules/custom/admin_notifications/translations/en.po --type=customized --override=all -y
echo   OK English importado
echo.

REM Francés
echo [3/5] Procesando Français (fr)...
drush language:add fr -y 2>nul
drush locale:import fr modules/custom/admin_notifications/translations/fr.po --type=customized --override=all -y
echo   OK Français importado
echo.

REM Portugués
echo [4/5] Procesando Português (pt-br)...
drush language:add pt-br -y 2>nul
drush locale:import pt-br modules/custom/admin_notifications/translations/pt-br.po --type=customized --override=all -y
echo   OK Português importado
echo.

REM Japonés
echo [5/5] Procesando 日本語 (ja)...
drush language:add ja -y 2>nul
drush locale:import ja modules/custom/admin_notifications/translations/ja.po --type=customized --override=all -y
echo   OK 日本語 importado
echo.

echo =========================================
echo Limpiando cache...
echo =========================================
drush cr
echo.

echo =========================================
echo Todas las traducciones se han importado!
echo =========================================
echo.
echo Para cambiar el idioma del sitio:
echo   1. Ve a Configuracion - Regional e idioma - Idiomas
echo   2. Selecciona el idioma predeterminado
echo.
echo O usa Drush:
echo   drush config:set system.site default_langcode es -y
echo.
pause
