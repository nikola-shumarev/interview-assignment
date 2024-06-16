
import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
  state: {
    bankCredits: []
  },
  mutations: {
    SET_BANK_CREDITS(state, bankCredits) {
      state.bankCredits = bankCredits;
    }
  },
  actions: {
    fetchBankCredits({ commit }) {
      axios.get('/bank-credits')
        .then(response => {
          commit('SET_BANK_CREDITS', response.data);
        })
        .catch(error => console.error('Error fetching bank credits:', error));
    }
  },
  getters: {
    bankCredits: state => state.bankCredits
  }
});
