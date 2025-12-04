<template>
  <q-page class="flex flex-center">
    <q-form
      @submit="onSubmit"
      @reset="onReset"
      class="q-gutter-md"
      autocomplete="off"
    >
      <h3>Inscription</h3>
      <q-input
        filled
        v-model="username"
        label="Votre pseudo *"
        lazy-rules
        :rules="[
          (val) => (val && val.length > 0) || 'Veillez saisir un pseudo',
        ]"
      />

      <q-input
        filled
        type="password"
        v-model="password"
        label="Mot de passe"
        lazy-rules
        :rules="[
          (val) =>
            (val !== null && val !== '') || 'Le mot de passe est obligatoire',
        ]"
      />

      <div>
        <q-btn label="S'inscrire" type="submit" color="primary" />
        <q-btn
          flat
          color="primary"
          to="/public/login"
          class="q-ml-sm"
          label="Connexion"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script setup>
import { ref } from "vue";
import { api } from "boot/axios";
import { useQuasar } from "quasar";
import { useRouter } from "vue-router";

const $q = useQuasar();
const router = useRouter();

const username = ref("");
const password = ref("");

function onReset() {
  username.value = "";
  password.value = "";
}

function onSubmit() {
  api
    .post("api/users", {
      username: username.value,
      plainPassword: password.value,
    })
    .then((res) => {
      $q.notify({
        type: "positive",
        message: "Vous êtes bien inscrit",
      });
      // Rediriger vers la page de connexion après inscription réussie
      setTimeout(() => {
        window.location.href = "/public/login";
      }, 1500);
    })
    .catch((e) => {
      let errorMessage = "Echec de l'inscription";

      if (e.response) {
        // Erreur de validation (422)
        if (e.response.status === 422 && e.response.data.violations) {
          const violations = e.response.data.violations;
          errorMessage = violations
            .map((v) => v.message)
            .join(", ");
        }
        // Erreur serveur (500) - peut être un utilisateur déjà existant
        else if (e.response.status === 500) {
          errorMessage = "Ce nom d'utilisateur est déjà utilisé";
        }
        // Autres erreurs
        else if (e.response.data?.detail) {
          errorMessage = e.response.data.detail;
        }
      } else if (e.request) {
        errorMessage = "Impossible de contacter le serveur";
      }

      $q.notify({
        type: "negative",
        message: errorMessage,
        icon: "report_problem",
      });
    });
}
</script>
