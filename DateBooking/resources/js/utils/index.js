const API_BASE_URL ='http://localhost:8000';

export const API_ROUTES = {
    servicios: `${API_BASE_URL}/api/servicios`,
    disponibilidad: {
        crear: `${API_BASE_URL}/api/disponibilidad`,
        toggle: (idServicio) => `${API_BASE_URL}/api/disponibilidad/${idServicio}/toggle`
    }
};

export default API_ROUTES; 