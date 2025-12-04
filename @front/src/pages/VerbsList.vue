<template>
  <div class="q-pa-md">
    <q-card class="q-mb-xl">
      <q-form @submit="save" autocomplete="off">
        <q-card-section>
          <div class="text-h6">Enregistrer un nouveau verbe</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-input v-model="newVerb.value" label="Verbe" class="q-mb-sm" />
          <q-select
            v-model="newVerb.type"
            :options="['indicatif', 'subjonctif']"
            label="Type"
            class="q-mb-md"
          />
          <q-select
            v-model="newVerb.time"
            :options="[
              'présent',
              'passé simple',
              'passé composé',
              'imparfait',
              'futur',
            ]"
            label="Temps"
            class="q-mb-md"
          />
          <q-input v-model="newVerb.s1" label="s1" class="q-mb-sm" />
          <q-input v-model="newVerb.s2" label="s2" class="q-mb-sm" />
          <q-input v-model="newVerb.s3" label="s3" class="q-mb-sm" />
          <q-input v-model="newVerb.p1" label="p1" class="q-mb-sm" />
          <q-input v-model="newVerb.p2" label="p2" class="q-mb-sm" />
          <q-input v-model="newVerb.p3" label="p3" class="q-mb-sm" />
        </q-card-section>
        <q-card-actions>
          <q-btn
            color="primary"
            type="submit"
            icon="save"
            unelevated
            label="Enregistrer"
          />
        </q-card-actions>
      </q-form>
    </q-card>
    <q-table
      v-if="verbs.length"
      title="Mots"
      :rows="verbs"
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

let verbs = ref("");
let newWord = ref("");
let newVerb = ref({
  value: "",
  type: "indicatif",
  time: "présent",
  s1: "",
  s2: "",
  s3: "",
  p1: "",
  p2: "",
  p3: "",
});
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
    name: "time",
    required: true,
    label: "temps",
    field: "time",
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

function getVerbs() {
  api.get("/api/verbs").then((res) => {
    verbs.value = res.data["hydra:member"].map((el) => {
      return {
        id: el.id,
        value: el.value,
        time: el.time,
        createdAt: date.formatDate(el.createdAt, "DD-MM-YYYY hh:mm"),
      };
    });
  });
}

function reset() {
  newVerb.value = {
    value: "",
    type: "indicatif",
    time: "présent",
    s1: "",
    s2: "",
    s3: "",
    p1: "",
    p2: "",
    p3: "",
  };
}

function save() {
  let v = newVerb.value;
  api
    .post("/api/verbs", {
      value: v.value,
      type: v.type,
      time: v.time,
      s1: v.s1,
      s2: v.s2,
      s3: v.s3,
      p1: v.p1,
      p2: v.p2,
      p3: v.p3,
    })
    .then((res) => {
      reset();
      $q.notify({
        type: "positive",
        message: "Le verbe a bien été enregistré",
      });
      getVerbs();
      newWord.value = "";
    })
    .catch((e) => {
      $q.notify({
        type: "negative",
        message: "Le verbe existe déjà",
      });
    });
}

function deleteWord() {
  selected.value.forEach((el) => {
    api.delete(`/api/verbs/${el.id}`).then(() => {
      getVerbs();
      $q.notify({
        type: "positive",
        message:
          selected.value.length > 1
            ? "Les verbes ont bien été supprimés"
            : "Le verbe a bien été supprimé",
      });
    });
  });
}
onMounted(() => {
  getVerbs();
});
</script>
