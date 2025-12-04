<template>
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
      </div>
    </div>
    <div class="row q-mb-xl">
      <div class="col">
        <the-clock
          v-if="randomTime"
          :meridiem="randomTime.meridiem === 'PM' ? 'après-midi' : 'matin'"
          :time="`2024-04-02T${randomTime.hour}:${randomTime.minute}:00+02:00`"
        />
      </div>
    </div>
    <div class="row q-mb-xl q-px-xl">
      <div class="col">
        <q-form @submit="check">
          <div class="row">
            <div class="col">
              <q-input
                class="q-mr-sm"
                label="heure"
                v-model="time.hour"
                :readonly="isChecking"
                filled
                style="font-size: 1.5em"
              />
            </div>
            <div class="col">
              <q-input
                label="minute"
                class="q-mb-sm"
                v-model="time.minute"
                :readonly="isChecking"
                filled
                style="font-size: 1.5em"
              />
            </div>
          </div>
          <q-btn
            unelevated
            label="valider"
            color="primary"
            type="submit"
            icon="send"
          />
          <transition v-if="result" appear enter-active-class="animated fadeIn">
            <q-btn
              @click="reload()"
              color="secondary"
              unelevated
              icon="restart_alt"
              label="Nouvelle heure"
              class="q-ml-sm"
            />
          </transition>
        </q-form>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { api } from "boot/axios";
import TheClock from "../components/TheClock.vue";
import { useUserStore } from "stores/user";

const store = useUserStore();

let time = ref({
  hour: "",
  minute: "",
});
let randomTime = ref(null);
let isChecking = ref(false);
let result = ref("");

/* let progress = computed(() => {
  return ((store.learnedWord.length / store.nbWords) * 100).toFixed(2) + "%";
}); */

function reload() {
  window.location.reload();
}
function reset() {
  time.value = {
    hour: "",
    minute: "",
  };
}
function check() {
  isChecking.value = true;
  if (
    randomTime.value.hour === time.value.hour &&
    randomTime.value.minute === time.value.minute
  ) {
    console.log("ok !");
    api.put(`/api/users/me/time/${randomTime.value.id}`, {}).then(() => {
      // store.getMyLearnedWords();
    });
    result.value = "ok";
  } else {
    console.log("ko !");
    result.value = "ko";
    api.post("/api/logs", {
      type: "user_answer",
      module: "clock",
      question: randomTime.value,
      answer: time.value,
      user: `/api/users/${store.user.id}`,
    });
  }
}

function getRandomTime() {
  result.value = "";
  isChecking.value = false;
  api.get("api/hours/random").then((res) => {
    console.log(res.data);
    randomTime.value = {
      id: res.data.id,
      hour: res.data.hour,
      minute: res.data.minute,
      meridiem: res.data.meridiem,
    };
  });
}
onMounted(() => {
  getRandomTime();
});
</script>

<style>
.word {
  font-family: "Kalam", cursive;
  font-weight: 400;
  font-style: normal;
}
</style>
