<template>
  <q-page class="flex flex-center">
    <q-form
      @submit="onSubmit"
      @reset="onReset"
      class="q-gutter-md"
      autocomplete="off"
    >
      <h3>Connexion</h3>
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
        <q-btn label="Connexion" type="submit" color="primary" />
        <q-btn
          to="/public/register"
          label="Inscription"
          color="primary"
          flat
          class="q-ml-sm"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script setup>
import { ref } from "vue";
import { api } from "boot/axios";
import { useQuasar } from "quasar";
const $q = useQuasar();
import { useRouter } from "vue-router";

const router = useRouter();

const username = ref("");
const password = ref("");

function onSubmit() {
  api
    .post("/auth", {
      username: username.value,
      password: password.value,
    })
    .then((res) => {
      $q.localStorage.set("dictation_user_token", res.data.token);
      $q.notify({
        type: "positive",
        message: "Vous êtes bien connecté",
      });
      console.log("route pish");
      router.push("/");
    })
    .catch((e) => {
      $q.notify({
        type: "negative",
        message: "Echec de l'inscription",
      });
    });
}
</script>
