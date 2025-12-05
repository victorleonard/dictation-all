import { boot } from "quasar/wrappers";
import { LocalStorage } from "quasar";
import axios from "axios";

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
// URL de base de l'API - utilise une variable d'environnement si disponible, sinon URL relative
// En production Docker, utilise le proxy Nginx qui communique avec le backend via le réseau interne
const apiBaseURL = import.meta.env.VITE_API_BASE_URL || "/api/";
const api = axios.create({ baseURL: apiBaseURL });

// Intercepteur de requête : ajoute le token JWT à chaque requête
api.interceptors.request.use(
  function (config) {
    const token = LocalStorage.getItem("dictation_user_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  function (error) {
    return Promise.reject(error);
  }
);

// Intercepteur de réponse : gère les erreurs 401 (non autorisé)
api.interceptors.response.use(undefined, function (error) {
  if (error && error.response) {
    const originalRequest = error.config;
    if (error.response.status === 401 && !originalRequest._retry) {
      LocalStorage.remove("dictation_user_token");
    }
  }
  return Promise.reject(error);
});

export default boot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api

  app.config.globalProperties.$axios = axios;
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api;
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
});

export { api };
