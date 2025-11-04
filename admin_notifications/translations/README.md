# Traducciones del Módulo Admin Notifications

Este directorio contiene las traducciones del módulo Admin Notifications para diferentes idiomas.

## Idiomas Disponibles

- **Inglés (en)**: `en.po`
- **Español (es)**: `es.po`
- **Francés (fr)**: `fr.po`
- **Portugués de Brasil (pt-BR)**: `pt-br.po`
- **Japonés (ja)**: `ja.po`

## Cómo Importar las Traducciones

### Opción 1: Usando la Interfaz de Drupal

1. Ve a **Configuración** → **Regional e idioma** → **Idiomas** (`/admin/config/regional/language`)
2. Agrega el idioma que desees usar
3. Ve a **Traducir interfaz** (`/admin/config/regional/translate/import`)
4. Selecciona el idioma
5. Sube el archivo `.po` correspondiente
6. Haz clic en "Importar"

### Opción 2: Usando Drush

```bash
# Importar traducción al español
drush language:add es
drush locale:import es modules/custom/admin_notifications/translations/es.po --type=customized --override=all

# Importar traducción al francés
drush language:add fr
drush locale:import fr modules/custom/admin_notifications/translations/fr.po --type=customized --override=all

# Importar traducción al portugués
drush language:add pt-br
drush locale:import pt-br modules/custom/admin_notifications/translations/pt-br.po --type=customized --override=all

# Importar traducción al japonés
drush language:add ja
drush locale:import ja modules/custom/admin_notifications/translations/ja.po --type=customized --override=all
```

### Opción 3: Colocación Automática (Recomendado)

Drupal puede detectar automáticamente las traducciones si colocas los archivos en la ubicación correcta:

```bash
# Crear directorios de traducción
mkdir -p translations

# Copiar archivos .po (ya están en el lugar correcto)
# Drupal debería detectarlos automáticamente
```

Luego:

1. Ve a **Configuración** → **Regional e idioma** → **Idiomas**
2. Agrega los idiomas que necesites
3. Ve a **Traducir interfaz** → **Actualizar traducciones**
4. Drupal importará automáticamente los archivos `.po` encontrados

## Cambiar el Idioma del Sitio

### Para todo el sitio:

1. Ve a **Configuración** → **Regional e idioma** → **Idiomas** (`/admin/config/regional/language`)
2. Selecciona el idioma predeterminado del sitio

### Por usuario:

1. Cada usuario puede cambiar su idioma en su perfil
2. Ve a **Mi cuenta** → **Editar**
3. En la pestaña "Idioma", selecciona el idioma preferido

## Verificar las Traducciones

Después de importar, verifica que las traducciones estén activas:

1. Cambia el idioma del sitio al que importaste
2. Ve a **Configuración** → **Sistema** → **Admin Notifications**
3. El menú y todos los textos deberían estar en el idioma seleccionado

## Textos Traducidos

Las traducciones incluyen:

- ✅ Títulos de menú y navegación
- ✅ Permisos del módulo
- ✅ Etiquetas de formularios
- ✅ Botones y acciones
- ✅ Mensajes del sistema
- ✅ Configuraciones
- ✅ Encabezados de tabla

## Agregar Nuevos Idiomas

Para agregar un nuevo idioma:

1. Copia uno de los archivos `.po` existentes
2. Renómbralo según el código de idioma (ej: `de.po` para alemán)
3. Actualiza el encabezado del archivo:
   ```
   "Language-Team: German\n"
   "Language: de\n"
   ```
4. Traduce las cadenas `msgstr` al nuevo idioma
5. Importa el archivo usando uno de los métodos anteriores

## Actualizar Traducciones Existentes

Si modificas el módulo y agregas nuevas cadenas traducibles:

1. Extrae las nuevas cadenas usando:
   ```bash
   drush locale:export es > admin_notifications_new.po
   ```
2. Agrega las nuevas traducciones al archivo `.po`
3. Re-importa el archivo actualizado

## Soporte

Para más información sobre el sistema de traducciones de Drupal:
- https://www.drupal.org/docs/multilingual-guide
- https://www.drupal.org/docs/8/modules/interface-translation

## Contribuciones

Si deseas contribuir con traducciones a otros idiomas, por favor:
1. Crea un nuevo archivo `.po` basado en `en.po`
2. Traduce todas las cadenas `msgstr`
3. Envía el archivo como contribución al proyecto
