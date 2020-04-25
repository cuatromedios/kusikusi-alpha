import Vue from 'vue'
import { LocalStorage } from 'quasar'
import _ from 'lodash'

// initial state
const state = {
  config: {},
  lang: 'en',
  editorLang: null,
  currentTitle: '',
  loading: false,
  toolbar: {
    editButton: false,
    saveButton: false
  },
  menuItems: {
    dashboard: {
      label: 'dashboard.title',
      icon: 'dashboard',
      name: 'dashboard'
    },
    content: {
      label: 'content.title',
      icon: 'home',
      name: 'content'
    },
    media: {
      label: 'media.title',
      icon: 'photo',
      name: 'media'
    },
    users: {
      label: 'users.title',
      icon: 'supervised_user_circle',
      name: 'users'
    },
    configuration: {
      label: 'settings.title',
      icon: 'settings_applications',
      name: 'settings'
    },
    logout: {
      label: 'login.logout',
      icon: 'exit_to_app',
      name: 'login'
    }
  }
}

// getters
const getters = {
  langs: (state) => {
    return _.get(state, 'config.langs', [])
  },
  defaultLang: (state) => {
    if (state.config && state.config.langs) {
      return state.config.langs[0]
    } else {
      return ''
    }
  },
  menu: (state, getters, rootState) => {
    let menu = _.clone(_.get(state, `config.menu.${rootState.session.user.profile}`))
    if (!menu) {
      if (rootState.session.user.profile === 'admin') {
        menu = [state.menuItems.content]
      } else {
        menu = [state.menuItems.content]
      }
    }
    menu.push(state.menuItems.logout)
    return menu
  }
}

// actions
const actions = {
  async getCmsConfig ({ commit }) {
    const configResult = await Vue.prototype.$api.get('/config/cms')
    commit('setCms', configResult.result)
    let editorLang = LocalStorage.getItem('editorLang')
    if (!editorLang || editorLang === '') {
      editorLang = configResult.result.langs[0] || 'en'
    }
    commit('setEditorLang', editorLang)
  }
}

// mutations
const mutations = {
  setConfig (state, newConfig) {
    state.config = newConfig
  },
  setTitle (state, newTitle) {
    state.currentTitle = newTitle
  },
  setLang (state, newLang) {
    LocalStorage.set('lang', newLang)
    state.lang = newLang
    Vue.i18n.set(newLang)
  },
  setEditorLang (state, newLang) {
    LocalStorage.set('editorLang', newLang)
    state.editorLang = newLang
  },
  setEditButton (state, show) {
    state.toolbar.editButton = show
  },
  setSaveButton (state, show) {
    state.toolbar.saveButton = show
  }
}

export default {
  namespaced: false,
  state,
  getters,
  actions,
  mutations
}
