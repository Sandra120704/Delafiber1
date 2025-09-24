// utils/alerts.js
// Centralizamos funciones para SweetAlert2

/**
 * Muestra un mensaje de éxito
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto adicional
 */
export function showSuccess(title = 'Éxito', text = '') {
  return Swal.fire({
    icon: 'success',
    title,
    text,
    confirmButtonColor: '#28a745',
    confirmButtonText: 'Aceptar'
  });
}

/**
 * Muestra un mensaje de error
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto adicional
 */
export function showError(title = 'Error', text = '') {
  return Swal.fire({
    icon: 'error',
    title,
    text,
    confirmButtonColor: '#e74c3c',
    confirmButtonText: 'Cerrar'
  });
}

/**
 * Muestra un mensaje de advertencia / confirmación
 * @param {string} title - Título del mensaje
 * @param {string} text - Texto adicional
 */
export function showConfirm(title = '¿Estás seguro?', text = '') {
  return Swal.fire({
    title,
    text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí',
    cancelButtonText: 'Cancelar'
  });
}
