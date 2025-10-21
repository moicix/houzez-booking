# Houzez Booking Plugin

Plugin de integración entre Houzez y MotoPress Hotel Booking para gestión completa de reservas de propiedades.

## 📁 Estructura del Plugin

```
houzez-booking/
├── houzez-booking.php                 # Archivo principal
├── includes/
│   ├── class-api-handler.php          # Manejador de API REST
│   ├── class-accommodation-sync.php   # Sincronización de alojamientos
│   └── class-availability-checker.php # Verificador de disponibilidad
├── admin/
│   └── settings.php                   # Página de configuración
├── assets/
│   ├── css/
│   │   ├── houzez-booking.css        # Estilos frontend
│   │   └── admin.css                  # Estilos admin
│   └── js/
│       ├── houzez-booking.js         # Scripts frontend
│       └── admin.js                   # Scripts admin
└── README.md                          # Este archivo
```

## 🚀 Instalación

1. Asegúrate de tener instalado:
   - WordPress 5.8 o superior
   - PHP 7.4 o superior
   - Tema Houzez activo
   - Plugin MotoPress Hotel Booking activo

2. Sube la carpeta `houzez-booking` a `/wp-content/plugins/`

3. Activa el plugin desde el panel de WordPress

4. Ve a **Houzez Booking > Configuración** para configurar

## ⚙️ Configuración Inicial

### Paso 1: Configuración General
- Activa la sincronización automática
- Habilita el widget de disponibilidad
- Configura el símbolo de moneda

### Paso 2: Habilitar Reservas en Propiedades
1. Edita una propiedad en Houzez
2. En la barra lateral derecha, busca el metabox "Houzez Booking - Sincronización"
3. Marca la casilla "Habilitar reservas para esta propiedad"
4. Guarda la propiedad
5. Haz clic en "Sincronizar ahora" si la sincronización automática está deshabilitada

## 🔌 Endpoints de API REST

Base URL: `https://tusitio.com/wp-json/houzez-booking/v1/`

### 1. Crear/Actualizar Tipo de Alojamiento
```http
POST /accommodation-types
```

**Parámetros:**
```json
{
  "title": "Villa de Lujo",
  "description": "Hermosa villa con vista al mar",
  "adults": 4,
  "children": 2,
  "total_capacity": 6,
  "size": 250.5,
  "property_id": 123
}
```

### 2. Verificar Disponibilidad
```http
POST /check-availability
```

**Parámetros:**
```json
{
  "check_in_date": "2025-11-01",
  "check_out_date": "2025-11-05",
  "adults": 2,
  "children": 0,
  "accommodation_type": 45
}
```

### 3. Disponibilidad de Propiedad Específica
```http
GET /property/{property_id}/availability?check_in_date=2025-11-01&check_out_date=2025-11-05&adults=2
```

### 4. Crear Reserva
```http
POST /bookings
```

**Parámetros:**
```json
{
  "check_in_date": "2025-11-01",
  "check_out_date": "2025-11-05",
  "property_id": 123,
  "reserved_accommodations": [
    {
      "accommodation_id": 45,
      "adults": 2,
      "children": 0
    }
  ],
  "customer": {
    "first_name": "Juan",
    "last_name": "Pérez",
    "email": "juan@email.com",
    "phone": "+52 614 123 4567",
    "country": "MX",
    "city": "Chihuahua"
  },
  "note": "Llegada tarde"
}
```

### 5. Obtener Reservas de Propiedad
```http
GET /property/{property_id}/bookings
```

## 📝 Ejemplos de Uso

### Ejemplo 1: Verificar Disponibilidad con JavaScript

```javascript
// En tu tema o plugin
jQuery(document).ready(function($) {
    $('#check-availability-btn').on('click', function() {
        $.ajax({
            url: '/wp-json/houzez-booking/v1/check-availability',
            type: 'POST',
            data: {
                check_in_date: '2025-11-01',
                check_out_date: '2025-11-05',
                adults: 2,
                children: 0,
                accommodation_type: 45
            },
            success: function(response) {
                if (response.availability && response.availability.length > 0) {
                    console.log('Disponible!', response);
                } else {
                    console.log('No disponible');
                }
            }
        });
    });
});
```

### Ejemplo 2: Crear Reserva Programáticamente

```php
// En functions.php o en tu plugin
function crear_reserva_personalizada() {
    $api_handler = new HZB_API_Handler();
    
    $request = new WP_REST_Request('POST', '/houzez-booking/v1/bookings');
    $request->set_body_params(array(
        'check_in_date' => '2025-11-01',
        'check_out_date' => '2025-11-05',
        'reserved_accommodations' => array(
            array(
                'accommodation_id' => 45,
                'adults' => 2,
                'children' => 0
            )
        ),
        'customer' => array(
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan@email.com',
            'phone' => '+52 614 123 4567'
        )
    ));
    
    $response = $api_handler->create_booking($request);
    
    if (!is_wp_error($response)) {
        echo 'Reserva creada: ID ' . $response['id'];
    }
}
```

### Ejemplo 3: Usar el Shortcode

```
[houzez_booking_availability property_id="123"]
```

O en una plantilla de tema:
```php
<?php echo do_shortcode('[houzez_booking_availability]'); ?>
```

## 🎨 Personalización del Widget

### Sobrescribir estilos CSS

```css
/* En tu tema hijo o CSS personalizado */

.hzb-availability-widget {
    background: #f9f9f9;
    border-radius: 12px;
}

.hzb-availability-form .form-control {
    border-color: #00aaef;
}

.hzb-availability-result.available {
    background: #e6f7ff;
    border-color: #00aaef;
}
```

### Modificar el widget con filtros

```php
// Cambiar el texto del botón
add_filter('hzb_availability_button_text', function($text) {
    return 'Comprobar fechas';
});

// Modificar opciones de adultos
add_filter('hzb_adults_options', function($options) {
    return range(1, 20); // Hasta 20 adultos
});
```

## 🔧 Hooks Disponibles

### Actions

```php
// Después de sincronizar una propiedad
do_action('hzb_after_sync_property', $property_id, $accommodation_id);

// Antes de crear una reserva
do_action('hzb_before_create_booking', $booking_data);

// Después de crear una reserva
do_action('hzb_after_create_booking', $booking_id, $property_id);
```

### Filters

```php
// Modific