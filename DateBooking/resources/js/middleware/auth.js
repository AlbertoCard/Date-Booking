// Middleware de autenticación
export default async function auth(to, from, next) {
  const auth = await import('../firebase').then(module => module.auth);
  
  // Esperar a que Firebase inicialice
  await new Promise((resolve) => {
    const unsubscribe = auth.onAuthStateChanged((user) => {
      unsubscribe();
      resolve(user);
    });
  });

  // Verificar si la ruta requiere autenticación
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  
  if (!requiresAuth) {
    next();
    return;
  }

  if (!auth.currentUser) {
    next('/login');
    return;
  }

  next();
} 