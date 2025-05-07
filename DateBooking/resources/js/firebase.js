// resources/js/firebase.js
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider } from "firebase/auth";

// Configuración de tu proyecto Firebase
const firebaseConfig = {
    apiKey: "AIzaSyDMfNoxrBZhTC0X-HBT4Bu5oC1kfxfrMmQ",
    authDomain: "datebooking-4ea0d.firebaseapp.com",
    projectId: "datebooking-4ea0d",
    storageBucket: "datebooking-4ea0d.firebasestorage.app",
    messagingSenderId: "683037419212",
    appId: "1:683037419212:web:61bf476167c53d9dfbc469"
};
  
const app = initializeApp(firebaseConfig);

// Servicios de autenticación
const auth = getAuth(app);
const googleProvider = new GoogleAuthProvider();

// Exportaciones
export { auth, googleProvider };
