<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="bg-light-blue-10">
      <q-toolbar>
        <!-- <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        /> -->
        <q-toolbar-title>
          <span class="title"> Révision de mots </span>
        </q-toolbar-title>
        <q-btn
          flat
          dense
          no-wrap
          icon="school"
          no-caps
          label="Mots"
          to="/"
          class="q-ml-sm q-px-md"
        />
        <q-btn
          flat
          dense
          no-wrap
          icon="school"
          no-caps
          label="Verbes"
          to="/verb"
          class="q-ml-sm q-px-md"
        >
          <q-badge color="orange-10" rounded floating
            >{{ store.learnedVerb.length }} / {{ store.nbVerbs }}</q-badge
          >
        </q-btn>
        <q-btn
          flat
          dense
          no-wrap
          icon="schedule"
          no-caps
          label="Horloge"
          to="/clock"
          class="q-ml-sm q-px-md"
        />
        <!-- <q-btn
          flat
          dense
          no-wrap
          icon="liste"
          no-caps
          label="Liste de mots"
          to="/list"
          class="q-ml-sm q-px-md"
        /> -->
        <q-btn
          flat
          dense
          no-wrap
          icon="done_all"
          no-caps
          label="Mots appris"
          to="/my-list"
          class="q-ml-sm q-px-md"
        >
          <q-badge color="orange-10" rounded floating
            >{{ store.learnedWord.length }} / {{ store.nbWords }}</q-badge
          >
        </q-btn>
        <div class="q-pl-md q-gutter-sm row no-wrap items-center">
          <q-btn flat>
            <q-menu>
              <q-list style="min-width: 100px">
                <q-item clickable v-close-popup @click="logout">
                  <q-item-section>Se deconnecter</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
            <span class="q-mr-sm">{{ store.user.username }}</span>
            <q-avatar size="26px">
              <img src="https://cdn.quasar.dev/img/boy-avatar.png" />
            </q-avatar>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <!-- <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label
          header
        >
          Essential Links
        </q-item-label>

        <EssentialLink
          v-for="link in linksList"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer> -->

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { onBeforeMount } from "vue";
import { api } from "boot/axios";
import { useUserStore } from "stores/user";
import { useQuasar } from "quasar";
const $q = useQuasar();

const store = useUserStore();
const router = useRouter();

function getMe() {
  api
    .get("api/users/me")
    .then((res) => {
      store.setUser(res.data);
    })
    .catch((e) => {
      router.push("public/login");
      console.log(e);
    });
}
onBeforeMount(() => {
  store.getNbWords();
  store.getMyLearnedWords();
  store.getMyLearnedVerbs();
  store.getNbVerbs();
  getMe();
});

function logout() {
  $q.localStorage.remove("dictation_user_token");
  router.push("public/login");
}

defineOptions({
  name: "MainLayout",
});

const linksList = [
  {
    title: "Docs",
    caption: "quasar.dev",
    icon: "school",
    link: "https://quasar.dev",
  },
  {
    title: "Github",
    caption: "github.com/quasarframework",
    icon: "code",
    link: "https://github.com/quasarframework",
  },
  {
    title: "Discord Chat Channel",
    caption: "chat.quasar.dev",
    icon: "chat",
    link: "https://chat.quasar.dev",
  },
  {
    title: "Forum",
    caption: "forum.quasar.dev",
    icon: "record_voice_over",
    link: "https://forum.quasar.dev",
  },
  {
    title: "Twitter",
    caption: "@quasarframework",
    icon: "rss_feed",
    link: "https://twitter.quasar.dev",
  },
  {
    title: "Facebook",
    caption: "@QuasarFramework",
    icon: "public",
    link: "https://facebook.quasar.dev",
  },
  {
    title: "Quasar Awesome",
    caption: "Community Quasar projects",
    icon: "favorite",
    link: "https://awesome.quasar.dev",
  },
];

const leftDrawerOpen = ref(false);

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value;
}
</script>

<style>
.title {
  font-family: "Kalam", cursive;
  font-weight: 400;
  font-style: normal;
}
</style>
