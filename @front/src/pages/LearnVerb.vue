<template>
  <!-- <q-linear-progress size="20px" :value="progress" color="light-blue-9">
    <div class="absolute-full flex flex-center">
      <q-badge color="white" text-color="accent" :label="progress" />
    </div>
  </q-linear-progress> -->
  <q-page>
    <div class="row">
      <div class="col">
        <transition v-if="result" appear enter-active-class="animated fadeIn">
          <q-card
            class="q-px-md"
            style="
              position: absolute;
              right: 20px;
              top: 20px;
              z-index: 999;
              background-color: #eee;
              transform: rotate(10deg);
              max-width: 200px;
            "
          >
            <div class="flex flex-center">
              <h4 class="q-my-sm" v-if="result === 'ok'">Bravo !</h4>
              <h4 class="text-subtitle1 q-my-sm" v-if="result === 'ko'">
                Tu feras mieux la prochaine fois !
              </h4>
              <q-icon
                :name="
                  result === 'ok'
                    ? 'emoji_emotions'
                    : 'sentiment_very_dissatisfied'
                "
                :class="result === 'ok' ? 'text-green' : 'text-red'"
                size="5em"
                style="margin: 0 auto"
              />
            </div>
          </q-card>
        </transition>
        <q-card class="q-mb-md" flat bordered style="background-color: #ddd">
          <div class="q-ml-auto q-mr-auto">
            <h3 class="text-center q-mb-sm q-mt-sm">{{ verb.value }}</h3>
            <p class="text-center">{{ `${verb.type} - ${verb.time}` }}</p>
          </div>
        </q-card>
      </div>
    </div>
    <div class="row q-mb-xl">
      <div class="col">
        <q-form @submit.prevent.stop="check" autocomplete="off">
          <q-card-section class="q-pt-none">
            <q-input
              v-model="newVerb.s1"
              label="1ère personne du singulier"
              lazy-rules
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.s1 !== verb.s1"
              spellcheck="false"
            />
            <q-input
              v-model="newVerb.s2"
              label="2ème personne du singulier"
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.s2 !== verb.s2"
              spellcheck="false"
            />
            <q-input
              v-model="newVerb.s3"
              label="3ème personne du singulier"
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.s3 !== verb.s3"
              spellcheck="false"
            />
            <q-input
              v-model="newVerb.p1"
              label="1ère personne du pluriel"
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.p1 !== verb.p1"
              spellcheck="false"
            />
            <q-input
              v-model="newVerb.p2"
              label="2ème personne du pluriel"
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.p2 !== verb.p2"
              spellcheck="false"
            />
            <q-input
              v-model="newVerb.p3"
              label="3ème personne du pluriel"
              :readonly="isChecking"
              class="q-mb-sm"
              :error="isChecking && newVerb.p3 !== verb.p3"
              spellcheck="false"
            />
          </q-card-section>
          <q-card-actions>
            <q-btn color="primary" type="submit" unelevated label="Envoyer" />
            <transition
              v-if="result"
              appear
              enter-active-class="animated fadeIn"
            >
              <q-btn
                @click="
                  reset();
                  getRandomVerb();
                "
                color="secondary"
                unelevated
                icon="restart_alt"
                label="Nouveau verbe"
              />
            </transition>
          </q-card-actions>
        </q-form>
      </div>
      <div class="col" v-if="isChecking">
        <q-card-section class="q-pt-none">
          <q-input
            v-model="verb.s1"
            :rules="[(val) => !!val || 'Champ obligatoire']"
            readonly
            class="q-mb-sm"
          />
          <q-input
            v-model="verb.s2"
            readonly
            :rules="[(val) => !!val || 'Champ obligatoire']"
            class="q-mb-sm"
          />
          <q-input
            v-model="verb.s3"
            readonly
            :rules="[(val) => !!val || 'Champ obligatoire']"
            class="q-mb-sm"
          />
          <q-input
            v-model="verb.p1"
            readonly
            :rules="[(val) => !!val || 'Champ obligatoire']"
            class="q-mb-sm"
          />
          <q-input
            v-model="verb.p2"
            readonly
            :rules="[(val) => !!val || 'Champ obligatoire']"
            class="q-mb-sm"
          />
          <q-input
            v-model="verb.p3"
            readonly
            :rules="[(val) => !!val || 'Champ obligatoire']"
            class="q-mb-sm"
          />
        </q-card-section>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { api } from "boot/axios";
import { useUserStore } from "stores/user";

const store = useUserStore();

let isChecking = ref(false);
let result = ref("");

let verb = ref("");
let newVerb = ref({
  s1: "",
  s2: "",
  s3: "",
  p1: "",
  p2: "",
  p3: "",
});

/* let progress = computed(() => {
  return ((store.learnedWord.length / store.nbWords) * 100).toFixed(2) + "%";
}); */

function check() {
  isChecking.value = true;
  if (
    verb.value.s1 === newVerb.value.s1 &&
    verb.value.s2 === newVerb.value.s2 &&
    verb.value.s3 === newVerb.value.s3 &&
    verb.value.p1 === newVerb.value.p1 &&
    verb.value.p2 === newVerb.value.p2 &&
    verb.value.p3 === newVerb.value.p3
  ) {
    api.put(`/api/users/me/verb/${verb.value.id}`, {}).then(() => {
      store.getMyLearnedVerbs();
    });
    result.value = "ok";
  } else {
    result.value = "ko";
    api.post("/api/logs", {
      type: "user_answer",
      module: "verb",
      answer: newVerb.value,
      question: verb.value,
      user: `/api/users/${store.user.id}`,
    });
  }
}

function reset() {
  newVerb.value = {
    s1: "",
    s2: "",
    s3: "",
    p1: "",
    p2: "",
    p3: "",
  };
}

function getRandomVerb() {
  result.value = "";
  isChecking.value = false;
  api.get("/api/verbs/random").then((res) => {
    verb.value = {
      value: res.data.value,
      time: res.data.time,
      type: res.data.type,
      id: res.data.id,
      s1: res.data.s1,
      s2: res.data.s2,
      s3: res.data.s3,
      p1: res.data.p1,
      p2: res.data.p2,
      p3: res.data.p3,
    };
  });
}
onMounted(() => {
  getRandomVerb();
});
</script>

<style>
.word {
  font-family: "Kalam", cursive;
  font-weight: 400;
  font-style: normal;
}
</style>
