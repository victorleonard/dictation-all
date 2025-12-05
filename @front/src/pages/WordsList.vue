<template>
  <div class="q-pa-md">
    <q-card class="q-mb-xl">
      <q-form @submit="save" autocomplete="off">
        <q-card-section>
          <div class="text-h6">Enregistrer un nouveau mot</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-input v-model="newWord" label="Mot" class="q-mb-sm" />
          <q-select
            v-model="level"
            :options="['CE1', 'CE2', 'CM1', 'CM2', '6ème']"
            label="Niveau"
            class="q-mb-md"
          />
        </q-card-section>
        <q-card-actions>
          <q-btn
            color="primary"
            type="submit"
            icon="save"
            unelevated
            label="Enregistrer"
            :disable="!newWord.length"
          />
        </q-card-actions>
      </q-form>
    </q-card>
    <q-table
      v-if="words.length"
      title="Mots"
      :rows="words"
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
          @click="deleteWord"
        />
      </template>
    </q-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { api } from "boot/axios";
import { useQuasar, date } from "quasar";
const $q = useQuasar();

let words = ref("");
let newWord = ref("");
let level = ref("CE2");
let selected = ref([]);

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
    label: "Niveau",
    field: "level",
    align: "left",
  },
  {
    name: "createdAt",
    required: true,
    label: "Date de création",
    field: "createdAt",
    align: "left",
    sortable: true,
  },
];

function getWords() {
  api.get("/api/words").then((res) => {
    words.value = res.data["hydra:member"].map((el) => {
      return {
        id: el.id,
        value: el.value,
        level: el.level,
        createdAt: date.formatDate(el.createdAt, "DD-MM-YYYY hh:mm"),
      };
    });
  });
}

function save() {
  api
    .post("/api/words", {
      value: newWord.value,
      level: level.value,
    })
    .then((res) => {
      $q.notify({
        type: "positive",
        message: "Le mot a bien été enregistré",
      });
      getWords();
      newWord.value = "";
    })
    .catch((e) => {
      $q.notify({
        type: "negative",
        message: "Le mot existe déjà",
      });
    });
}

function deleteWord() {
  selected.value.forEach((el) => {
    api.delete(`/api/words/${el.id}`).then(() => {
      getWords();
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
onMounted(() => {
  getWords();
});
</script>
