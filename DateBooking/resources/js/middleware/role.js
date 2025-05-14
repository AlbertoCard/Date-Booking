import axios from 'axios';

// Middleware de verificación de roles
export default async function role(to, from, next) {
  const auth = await import('../firebase').then(module => module.auth);
  const currentUser = auth.currentUser;

  // Si la ruta no requiere rol específico, permitir acceso
  if (!to.meta.role) {
    next();
    return;
  }

  // Si no hay usuario autenticado, no verificamos rol
  if (!currentUser) {
    next();
    return;
  }

  try {
    // Intentar obtener el rol del localStorage primero
    let userRole = localStorage.getItem('userRole');

    // Si no hay rol en localStorage, obtenerlo de la API
    if (!userRole) {
      const response = await axios.get(`/api/usuarios/obtener/${currentUser.uid}`);
      userRole = response.data.usuario.rol;
      // Guardar el rol en localStorage
      localStorage.setItem('userRole', userRole);
    }

    // Verificar si la ruta requiere un rol específico
    const requiredRole = to.meta.role;
    
    if (requiredRole && requiredRole !== userRole) {
      // Si el usuario no tiene el rol correcto, redirigir al dashboard correspondiente
      next(userRole === 'cliente' ? '/dashboard-cliente' : '/dashboard-establecimiento');
      return;
    }

    // Si todo está bien, permitir acceso
    next();
  } catch (error) {
    console.error('Error al verificar rol:', error);
    // Limpiar el rol almacenado en caso de error
    localStorage.removeItem('userRole');
    next('/login');
  }
} 