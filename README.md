SMARTVIAL - Sistema Inteligente de Gestión Digital de Siniestros Viales

Proyecto MVP con arquitectura MVC en PHP 8, MySQL y frontend con HTML5, CSS3, Bootstrap 5 y JavaScript.

Instalación rápida:
- Colocar la carpeta del proyecto dentro de `htdocs` de XAMPP (ej: `C:/xampp/htdocs/smartvial`).
- Importar `sql/smartvial.sql` en phpMyAdmin.
- Ajustar credenciales en `app/config.php` si es necesario.
- Abrir en navegador `http://localhost/smartvial/public/`.

Contenido principal:
- `public/` - Front controller y recursos públicos
- `app/` - Controladores, modelos, helpers
- `views/` - Vistas (templates)
- `assets/` - CSS, JS, imágenes
- `sql/` - Script SQL para crear la BD
- `docs/` - Documentación, diseño y QA

# Test accounts created by tools:
- Admin: admin@smartvial.local / admin123
- Agente (demo): agente@demo.local / demo123

Security notes:
- CSRF protection added for POST forms (server-side validation + hidden inputs).
- Session cookie hardened (`HttpOnly`, `SameSite=Lax`) and session regenerated on login.
- File uploads validated by MIME and size; stored with random filenames.

# smart-vial