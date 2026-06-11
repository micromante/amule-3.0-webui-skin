# aMule 3.0.0 WebUI Skin

> Fork de [**AmuleWebUI-Reloaded Material Style**](https://github.com/MatteoRagni/AmuleWebUI-Reloaded)
> (de MatteoRagni), adaptado y mejorado para **aMule 3.0.0**.

Esta versión personaliza la plantilla original con varios arreglos y mejoras:

**Buscador**
- Soluciona el bug de aMule 3.0.0 por el que una nueva búsqueda no limpia los
  resultados anteriores (`RemoveResults(0xffffffff)` no coincide con ningún id):
  filtrado en cliente que muestra solo los resultados de la búsqueda actual.
- Envío de búsqueda por AJAX (urlencoded) y refresco solo de resultados, sin
  recargar la página entera.
- Filtro de texto dinámico (varios términos con AND) y contador de resultados.
- Ordenación por columnas en cliente (nombre/tamaño/fuentes), alternando asc/desc.
- Columna **Network** con el origen de la búsqueda (Kad / eD2k + servidor).
- Botón de descarga por fila y spinner de carga.

**Descargas**
- Refresco por AJAX solo de las tablas (sin recargar toda la página).
- Selects de filtro a la misma altura que los botones de la barra.

**Idiomas (i18n)**
- Traducción completa de la interfaz a **castellano** e **inglés**.
- Botón en el navbar para cambiar de idioma al instante; la preferencia se
  guarda en el navegador y se mantiene entre sesiones.
- Solo se traduce la interfaz: los resultados de búsqueda y los nombres de
  archivo se respetan tal cual.

**General**
- Fuentes más grandes, colores de botones/iconos legibles y ajustes de espaciado.

## Instalación en Raspberry Pi (Raspbian / Ubuntu)

La skin es un conjunto de archivos estáticos (PHP de plantilla, CSS y JS) que
viven en el directorio de plantillas de `amuleweb`. La ruta depende de cómo se
instaló aMule:

| Instalación | Directorio de plantillas |
|-------------|--------------------------|
| Compilado desde fuente (`/usr/local`) | `/usr/local/share/amule/webserver/` |
| Paquete `apt` (Raspbian/Ubuntu) | `/usr/share/amule/webserver/` |

> Comprueba cuál es la tuya con:
> ```bash
> ls /usr/local/share/amule/webserver/ /usr/share/amule/webserver/ 2>/dev/null
> ```
> En el resto del README se usa `/usr/local/share/amule/webserver` (compilado);
> sustitúyelo por `/usr/share/amule/webserver` si instalaste con `apt`.

### Opción A — Clonar con git directamente en la Raspberry (por SSH)

Entra en la Pi por SSH y clona el repo en el directorio de plantillas:

```bash
ssh pi@IP_DE_LA_RASPBERRY

sudo apt update && sudo apt install -y git          # si no tienes git
cd /usr/local/share/amule/webserver
sudo git clone https://github.com/micromante/amule-3.0-webui-skin.git
```

Esto crea `/usr/local/share/amule/webserver/amule-3.0-webui-skin/`.

### Opción B — Copiar desde tu ordenador por SSH (scp / rsync)

Si tienes los archivos en tu PC/Mac y no quieres usar git en la Pi, cópialos por
SSH. Como el destino requiere permisos de root, primero se sube a una carpeta
temporal y luego se mueve con `sudo`:

```bash
# Desde tu ordenador (no desde la Pi)
scp -r /ruta/local/amule-3.0-webui-skin pi@IP_DE_LA_RASPBERRY:/tmp/

# Luego, ya por SSH en la Pi:
ssh pi@IP_DE_LA_RASPBERRY
sudo mv /tmp/amule-3.0-webui-skin /usr/local/share/amule/webserver/
```

Alternativa con `rsync` (más rápido para actualizar, solo copia lo cambiado):

```bash
rsync -avz --rsync-path="sudo rsync" \
  /ruta/local/amule-3.0-webui-skin/ \
  pi@IP_DE_LA_RASPBERRY:/usr/local/share/amule/webserver/amule-3.0-webui-skin/
```

### Opción C — Copia directa (tarjeta SD / USB)

Si trabajas con la tarjeta SD montada en tu ordenador o con un USB en la Pi,
copia la carpeta `amule-3.0-webui-skin` completa dentro del directorio de
plantillas (`.../share/amule/webserver/`). El resultado debe ser el mismo:
una carpeta `amule-3.0-webui-skin` con los `.php`, `custom.css` e `i18n.js`.

### Activar la plantilla

`amuleweb` selecciona la plantilla por el **nombre de la carpeta**. Indícaselo
al arrancar:

```bash
amuleweb --template=amule-3.0-webui-skin
```

O déjalo fijo en el archivo de configuración de aMule. Normalmente está en la
carpeta del usuario que ejecuta el demonio:

```
/home/<usuario>/.aMule/amule.conf       # p. ej. /home/pi/.aMule/amule.conf
```

Edítalo (con el demonio **parado**, porque `amuled` reescribe el archivo al
cerrarse) y en la sección `[WebServer]` cambia la línea `Template`:

```bash
sudo systemctl stop amuled              # para el demonio antes de editar
nano /home/pi/.aMule/amule.conf
```

Busca la sección `[WebServer]` y deja `Template` con el **nombre de la carpeta**
de la skin. Ejemplo (las demás líneas son orientativas, la clave es `Template`):

```ini
[WebServer]
Enabled=1
Port=4711
Template=amule-3.0-webui-skin        ;  <-- LÍNEA A CAMBIAR (nombre de la carpeta)
Password=<hash_md5_de_tu_contraseña>
UPnPWebServerEnabled=0
```

- `Template` → debe coincidir **exactamente** con el nombre de la carpeta que
  copiaste en `.../share/amule/webserver/` (aquí `amule-3.0-webui-skin`).
- Si `Template` no existe en tu archivo, añádela tú dentro de `[WebServer]`.
- `Password` es el **hash MD5** de la contraseña de la web (no en texto plano).

Reinicia el servicio para aplicar los cambios (según cómo lo tengas):

```bash
sudo systemctl restart amuled        # si corre como servicio systemd
# o reinicia amuleweb manualmente
```

La interfaz queda disponible en `http://IP_DE_LA_RASPBERRY:4711`.

> **Nota:** `amuleweb` exige autenticación para servir los archivos (incluidos
> `custom.css` e `i18n.js`). Si tras instalar ves la web sin estilos o sin el
> selector de idioma, fuerza una recarga sin caché en el navegador
> (`Ctrl+Shift+R`).
