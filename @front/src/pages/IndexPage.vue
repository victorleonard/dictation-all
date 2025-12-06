<template>
  <q-linear-progress size="20px" :value="progress" color="light-blue-9">
    <div class="absolute-full flex flex-center">
      <q-badge color="white" text-color="accent" :label="progress" />
    </div>
  </q-linear-progress>
  <q-page class="flex flex-center">
    <div>
      <div class="row">
        <transition
          appear
          :enter-active-class="`animated ${
            result === 'ok' ? 'bounce' : 'shake'
          }`"
        >
          <q-icon
            v-if="result"
            :name="
              result === 'ok' ? 'emoji_emotions' : 'sentiment_very_dissatisfied'
            "
            :class="result === 'ok' ? 'text-green' : 'text-red'"
            size="10em"
            style="margin: 0 auto"
          />
        </transition>
      </div>
      <div class="row">
        <transition appear enter-active-class="animated fadeIn">
          <h3 v-if="seeWord" class="q-ml-auto q-mr-auto">{{ word.value }}</h3>
        </transition>
      </div>
      <div class="row q-mb-xl">
        <q-btn
          v-if="!result"
          style="margin: 0 auto"
          size="1.5em"
          class="q-mr-sm"
          color="primary"
          label="Écouter"
          icon="volume_up"
          unelevated
          :loading="isLoading"
          @click="say"
        />
      </div>
      <div class="row q-mb-md" v-if="word">
        <q-form @submit="check" autocomplete="off">
          <div style="margin: 0 auto; display: flex">
            <q-input
              standout
              v-model="userInput"
              :disable="result ? 'disable' : null"
              filled
              style="font-size: 2em"
            />
            <q-btn
              unelevated
              square
              color="primary"
              type="submit"
              icon="send"
              :disable="result.length || !userInput.length ? 'disable' : null"
            />
          </div>
        </q-form>
      </div>
      <div class="row q-mt-xl">
        <q-btn
          v-if="result"
          style="margin: 0 auto"
          color="primary"
          outline
          label="Nouveau mot"
          icon="games"
          unelevated
          @click="getRandomWord()"
        />
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted, computed, ref } from "vue";
import { api } from "boot/axios";
import { ElevenLabsClient } from "@elevenlabs/elevenlabs-js";
import { useUserStore } from "stores/user";

const store = useUserStore();

// Initialiser le client ElevenLabs avec la clé API depuis les variables d'environnement
const elevenLabsApiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
const elevenLabsClient = elevenLabsApiKey
  ? new ElevenLabsClient({ apiKey: elevenLabsApiKey })
  : null;

// Voice ID par défaut (vous pouvez le changer selon vos préférences)
// Exemple: "21m00Tcm4TlvDq8ikWAM" pour Rachel (voix anglaise)
// Vous pouvez utiliser n'importe quel voice_id depuis votre compte ElevenLabs
const defaultVoiceId = import.meta.env.VITE_ELEVENLABS_VOICE_ID || "21m00Tcm4TlvDq8ikWAM";

let word = ref("");

let seeWord = ref(false);
let userInput = ref("");
let result = ref("");
let isLoading = ref(false);

// Référence à l'audio actuel pour pouvoir l'arrêter si nécessaire
let currentAudio = ref(null);
let currentAudioUrl = ref(null);

let progress = computed(() => {
  return ((store.learnedWord.length / store.nbWords) * 100).toFixed(2) + "%";
});

defineOptions({
  name: "IndexPage",
});

function save() {
  api.put(`/api/users/me/word/${word.value.id}`, {}).then(() => {
    store.getMyLearnedWords();
  });
}

async function say() {
  if (!elevenLabsClient) {
    console.error("Clé API ElevenLabs non configurée. Veuillez définir VITE_ELEVENLABS_API_KEY dans votre fichier .env");
    return;
  }

  // Activer le loader
  isLoading.value = true;

  // Arrêter l'audio précédent s'il existe
  if (currentAudio.value) {
    currentAudio.value.pause();
    currentAudio.value.currentTime = 0;
    currentAudio.value = null;
  }

  // Nettoyer l'URL blob précédente
  if (currentAudioUrl.value) {
    URL.revokeObjectURL(currentAudioUrl.value);
    currentAudioUrl.value = null;
  }

  try {
    console.log("Utilisation d'ElevenLabs pour la synthèse vocale:", word.value.value);
    // Convertir le texte en audio avec ElevenLabs
    const audioStream = await elevenLabsClient.textToSpeech.convert(defaultVoiceId, {
      text: word.value.value,
      model_id: "eleven_multilingual_v2", // Modèle multilingue
      voice_settings: {
        stability: 0.5,
        similarity_boost: 0.75,
        style: 0.0,
        use_speaker_boost: true,
        speed: 0.7, // Vitesse de la voix (0.7 = lent, 1.0 = normal, 1.2 = rapide)
      },
    });

    // Convertir le stream en blob puis en URL pour l'audio
    const chunks = [];
    for await (const chunk of audioStream) {
      chunks.push(chunk);
    }
    const audioBlob = new Blob(chunks, { type: "audio/mpeg" });
    const audioUrl = URL.createObjectURL(audioBlob);
    currentAudioUrl.value = audioUrl;

    // Créer un élément audio et le jouer
    const audio = new Audio(audioUrl);
    currentAudio.value = audio;

    // Désactiver le loader une fois que l'audio est prêt à être joué
    isLoading.value = false;

    audio.play();

    // Nettoyer l'URL après la lecture
    audio.addEventListener("ended", () => {
      URL.revokeObjectURL(audioUrl);
      currentAudio.value = null;
      currentAudioUrl.value = null;
    });

    audio.addEventListener("error", (e) => {
      console.error("Erreur lors de la lecture audio:", e);
      URL.revokeObjectURL(audioUrl);
      currentAudio.value = null;
      currentAudioUrl.value = null;
    });
  } catch (error) {
    console.error("Erreur lors de la conversion texte vers audio:", error);
    currentAudio.value = null;
    currentAudioUrl.value = null;
    isLoading.value = false;
  }
}

function check() {
  console.log(userInput.value, word.value.value);
  if (
    userInput.value.trim().toLowerCase() ===
    word.value.value.trim().toLowerCase()
  ) {
    console.log("bravo");
    save();
    seeWord.value = true;
    result.value = "ok";
  } else {
    seeWord.value = true;
    result.value = "ko";
    api.post("/api/word_errors", {
      word: `/api/words/${word.value.id}`,
      value: userInput.value,
      user: `/api/users/${store.user.id}`,
    });
    console.log("ko");
  }
}

function getRandomWord() {
  // Arrêter l'audio en cours si un nouveau mot est chargé
  if (currentAudio.value) {
    currentAudio.value.pause();
    currentAudio.value.currentTime = 0;
    currentAudio.value = null;
  }
  if (currentAudioUrl.value) {
    URL.revokeObjectURL(currentAudioUrl.value);
    currentAudioUrl.value = null;
  }

  api.get("/api/words/random").then((res) => {
    word.value = res.data;
    result.value = "";
    userInput.value = "";
    seeWord.value = false;
  });
}
onMounted(() => {
  getRandomWord();
});
</script>

<style>
.word {
  font-family: "Kalam", cursive;
  font-weight: 400;
  font-style: normal;
}
</style>
