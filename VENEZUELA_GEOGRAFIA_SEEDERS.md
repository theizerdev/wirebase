# Seeders Geográficos para Venezuela

Este conjunto de seeders proporciona datos reales y actualizados de la división político-administrativa de Venezuela, incluyendo estados, municipios y parroquias.

## Estructura de Archivos

- `VenezuelaStatesSeeder.php`: Crea los 23 estados de Venezuela más el Distrito Capital
- `VenezuelaMunicipalitiesSeeder.php`: Crea los municipios asociados a cada estado
- `VenezuelaParishesSeeder.php`: Crea las parroquias asociadas a cada municipio
- `VenezuelaGeographicalSeeder.php`: Se encarga de ejecutar todos los seeders geográficos en orden

## Jerarquía Geográfica Actual

Debido a la estructura actual de las tablas en la base de datos, la jerarquía es:

1. Estado (estados)
2. Ciudad (ciudades) - *Temporalmente usada para mapear estados*
3. Municipio (municipios) - *Asociado a ciudades*
4. Parroquia (parroquias) - *Asociado a municipios*

## Datos Incluidos

### Estados
Todos los 23 estados de Venezuela más el Distrito Capital:

- Amazonas (Z)
- Anzoátegui (B)
- Apure (C)
- Aragua (D)
- Barinas (E)
- Bolívar (F)
- Carabobo (G)
- Cojedes (H)
- Delta Amacuro (Y)
- Distrito Capital (A)
- Falcón (I)
- Guárico (J)
- Lara (K)
- Mérida (L)
- Miranda (M)
- Monagas (N)
- Nueva Esparta (O)
- Portuguesa (P)
- Sucre (R)
- Táchira (S)
- Trujillo (T)
- Vargas (X)
- Yaracuy (U)
- Zulia (V)

### Municipios y Parroquias
Los seeders incluyen una selección representativa de municipios y parroquias. La lista completa de parroquias en Venezuela supera las 1000, por lo que se incluye una muestra representativa de las más importantes.

## Instrucciones de Uso

Para ejecutar todos los seeders geográficos de Venezuela:

```bash
php artisan db:seed --class=VenezuelaGeographicalSeeder
```

O para ejecutarlos individualmente:

```bash
php artisan db:seed --class=VenezuelaStatesSeeder
php artisan db:seed --class=VenezuelaMunicipalitiesSeeder  
php artisan db:seed --class=VenezuelaParishesSeeder
```

## Notas Importantes

1. Es necesario ejecutar primero `PaisSeeder` para que el país Venezuela esté disponible
2. Los códigos de los estados siguen el estándar oficial de Venezuela
3. Los datos se basan en fuentes oficiales del Instituto Nacional de Estadística (INE) de Venezuela
4. Todos los registros se crean con el campo `activo` en `true`

## Consideraciones sobre la Estructura

La jerarquía actual (Estado → Ciudad → Municipio → Parroquia) puede no reflejar completamente la división político-administrativa real de Venezuela, pero se ajusta a la estructura existente de las tablas en la base de datos. En una futura mejora, podría considerarse una reestructuración para que sea Estado → Municipio → Parroquia, que es más fiel a la realidad geográfica venezolana.