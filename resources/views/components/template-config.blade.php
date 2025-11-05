@if(isset($templateSettings))
<script>
// Aplicar configuración de plantilla desde base de datos
document.addEventListener('DOMContentLoaded', function() {
  try {
    const dbSettings = @json($templateSettings->toJsConfig());
    const html = document.documentElement;
    
    if (!html) return;
    
    // Aplicar configuraciones al HTML
    if (dbSettings.defaultTheme) {
      html.setAttribute('data-bs-theme', dbSettings.defaultTheme);
    }
    
    if (dbSettings.defaultSkin !== undefined) {
      html.setAttribute('data-skin', dbSettings.defaultSkin == 1 ? 'bordered' : 'default');
    }
    
    if (dbSettings.defaultTextDir) {
      html.setAttribute('dir', dbSettings.defaultTextDir);
    }
    
    // Aplicar color primario
    if (dbSettings.defaultPrimaryColor) {
      html.style.setProperty('--bs-primary', dbSettings.defaultPrimaryColor);
      const rgb = hexToRgb(dbSettings.defaultPrimaryColor);
      if (rgb) {
        html.style.setProperty('--bs-primary-rgb', rgb);
      }
    }
    
    // Aplicar al TemplateCustomizer si existe
    if (typeof window.templateCustomizer !== 'undefined' && window.templateCustomizer) {
      Object.keys(dbSettings).forEach(key => {
        if (dbSettings[key] !== null && dbSettings[key] !== undefined) {
          window.templateCustomizer.settings[key] = dbSettings[key];
        }
      });
      
      if (typeof window.templateCustomizer._updateOptions === 'function') {
        window.templateCustomizer._updateOptions();
      }
    }
  } catch (error) {
    console.warn('Error applying template settings:', error);
  }
});

// Función auxiliar para convertir hex a rgb
function hexToRgb(hex) {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? 
    parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16) :
    null;
}
</script>
@endif