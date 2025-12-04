import { defineStore } from "pinia";
import { api } from "boot/axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: "",
    learnedWord: [],
    learnedVerb: [],
    nbWords: 0,
    nbVerbs: 0,
  }),
  getters: {},
  actions: {
    setUser(value) {
      this.user = value;
    },
    getMyLearnedWords() {
      api.get("/api/users/me/words").then((res) => {
        this.learnedWord = res.data["hydra:member"];
      });
    },
    getMyLearnedVerbs() {
      api.get("/api/users/me/verbs").then((res) => {
        this.learnedVerb = res.data["hydra:member"];
      });
    },
    getNbWords() {
      api.get("/api/words").then((res) => {
        this.nbWords = res.data["hydra:totalItems"];
      });
    },
    getNbVerbs() {
      api.get("/api/verbs").then((res) => {
        this.nbVerbs = res.data["hydra:totalItems"];
      });
    },
  },
});
