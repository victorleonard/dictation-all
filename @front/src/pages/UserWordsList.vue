<template>
  <div class="q-pa-md">
    <q-table
      title="Mots"
      :rows="store.learnedWord"
      :columns="columns"
      row-key="value"
      v-model:selected="selected"
      :pagination="{
        rowsPerPage: 200,
      }"
      no-data-label="Vous n'avez pas encore appris de mots"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useUserStore } from "stores/user";
const store = useUserStore();

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
];

onMounted(() => {
  store.getMyLearnedWords();
});
</script>
