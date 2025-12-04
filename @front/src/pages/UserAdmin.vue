<template>
  <q-page class="q-pa-md">
    <q-select
      class="q-mb-xl"
      v-model="user"
      filled
      :options="users"
      label="Users"
    />

    <div v-if="user">
      <q-tabs
        v-model="tab"
        dense
        class="text-grey"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        narrow-indicator
      >
        <q-tab name="wordsError" label="Word Errors" />
        <!--  <q-tab name="alarms" label="Alarms" />
        <q-tab name="movies" label="Movies" /> -->
      </q-tabs>

      <q-separator />

      <q-tab-panels v-model="tab" animated>
        <q-tab-panel name="wordsError">
          <q-table
            :rows="wordErrors"
            :columns="columns"
            row-key="value"
            :pagination="{
              rowsPerPage: 200,
            }"
            selection="multiple"
            v-model:selected="selected"
          >
            <template v-slot:top>
              <q-btn
                color="red"
                label="supprimer"
                icon="delete"
                unelevated
                :disable="!selected.length"
                size=""
                @click="deleteWordError"
              />
            </template>
          </q-table>
        </q-tab-panel>

        <!-- <q-tab-panel name="alarms">
          <div class="text-h6">Alarms</div>
          Lorem ipsum dolor sit amet consectetur adipisicing elit.
        </q-tab-panel>

        <q-tab-panel name="movies">
          <div class="text-h6">Movies</div>
          Lorem ipsum dolor sit amet consectetur adipisicing elit.
        </q-tab-panel> -->
      </q-tab-panels>
    </div>
  </q-page>
</template>

<script setup>
import { api } from "src/boot/axios";
import { onMounted, ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useQuasar } from "quasar";
const $q = useQuasar();

const router = useRouter();
const route = useRoute();

const users = ref([]);
const user = ref("");
const tab = ref("wordsError");
let selected = ref([]);

const wordErrors = ref([]);

const columns = [
  {
    name: "value",
    required: true,
    label: "Mot",
    field: "value",
    align: "left",
    sortable: true,
  },
  {
    name: "level",
    required: true,
    label: "Erreur",
    field: "error",
    align: "left",
  },
];

watch(user, (newUser) => {
  router.push({
    params: {
      id: newUser.value,
    },
  });
});

watch(
  () => route.params.id,
  (newValue) => {
    getWordErrors(newValue);
  }
);

watch(user, (newUser) => {
  router.push({
    params: {
      id: newUser.value,
    },
  });
});

function deleteWordError() {
  selected.value.forEach((el) => {
    api.delete(`/api/word_errors/${el.id}`).then(() => {
      getWordErrors(route.params.id);
      $q.notify({
        type: "positive",
        message:
          selected.value.length > 1
            ? "Les mots ont bien été supprimés"
            : "Le mot a bien été supprimé",
      });
    });
  });
}

function getUsers() {
  api.get("api/users").then((res) => {
    users.value = res.data["hydra:member"].map((el) => {
      return {
        value: el.id,
        label: el.username,
      };
    });
    if (route.params.id) {
      // getWordErrors(route.params.id);
      user.value = users.value.find((el) => el.value === route.params.id);
      if (tab.value === "wordsError") {
        getWordErrors(route.params.id);
      }
    }
  });
}
function getWordErrors(id) {
  api.get(`api/words_error/${id}`).then((res) => {
    if (!res.data) {
      wordErrors.value = [];
      return;
    }
    wordErrors.value = res.data["hydra:member"].map((el) => {
      return {
        id: el.id,
        value: el.word.value,
        error: el.value,
      };
    });
  });
}
//created
onMounted(() => {
  getUsers();
});
</script>
