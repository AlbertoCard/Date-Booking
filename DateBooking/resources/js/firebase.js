// resources/js/firebase.js
import { initializeApp } from 'firebase/app';
import { getAuth } from 'firebase/auth';

// Configuración de tu proyecto Firebase
const firebaseConfig = {
    apiKey: "AIzaSyDMfNoxrBZhTC0X-HBT4Bu5oC1kfxfrMmQ",
    authDomain: "datebooking-4ea0d.firebaseapp.com",
    projectId: "datebooking-4ea0d",
    storageBucket: "datebooking-4ea0d.firebasestorage.app",
    messagingSenderId: "683037419212",
    appId: "1:683037419212:web:61bf476167c53d9dfbc469"
};
  

const firebaseApp = initializeApp(firebaseConfig);

// Obtenemos la instancia de Auth
const auth = getAuth(firebaseApp);

export { auth };
