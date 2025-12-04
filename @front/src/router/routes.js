const routes = [
  {
    path: "/public",
    component: () => import("layouts/PublicLayout.vue"),
    children: [
      {
        path: "register",
        component: () => import("src/pages/UserRegister.vue"),
      },
      {
        path: "login",
        component: () => import("src/pages/UserLogin.vue"),
      },
    ],
  },
  {
    path: "/",
    component: () => import("src/layouts/PrivateLayout.vue"),
    children: [
      { path: "", component: () => import("pages/IndexPage.vue") },
      { path: "list", component: () => import("src/pages/WordsList.vue") },
      { path: "verbs", component: () => import("src/pages/VerbsList.vue") },
      { path: "verb", component: () => import("src/pages/LearnVerb.vue") },
      { path: "clock", component: () => import("src/pages/LearnClock.vue") },
      {
        path: "my-list",
        component: () => import("src/pages/UserWordsList.vue"),
      },
      {
        path: "admin/:id?",
        component: () => import("src/pages/UserAdmin.vue"),
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/ErrorNotFound.vue"),
  },
];

export default routes;
