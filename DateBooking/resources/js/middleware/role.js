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
    // Intentar obtener los datos del usuario del localStorage primero
    let userData = localStorage.getItem('userData');
    let userRole = null;

    if (userData) {
      // Si existe, parseamos los datos
      userData = JSON.parse(userData);
      userRole = userData.rol;
    } else {
      // Si no hay datos en localStorage, obtenerlos de la API
      const response = await axios.get(`/api/usuarios/obtener/${currentUser.uid}`);
      userData = response.data.usuario;
      userRole = userData.rol;
      
      // Guardar todos los datos del usuario en localStorage
      localStorage.setItem('userData', JSON.stringify(userData));
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
    // Limpiar los datos almacenados en caso de error
    localStorage.removeItem('userData');
    next('/login');
  }
} 