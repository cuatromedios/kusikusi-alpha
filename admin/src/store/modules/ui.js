import Vue from 'vue'
import { LocalStorage } from 'quasar'
import _ from 'lodash'

// initial state
const state = {
  config: {},
  lang: 'en',
  uiLang: null,
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
  media_url: () => {
    return process.env.MEDIA_URL
  },
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
  },
  iconOf: (state) => (model) => {
    return _.get(state, `config.models[${model}].icon`, 'blur_on')
  },
  nameOf: (state) => (model) => {
    return _.get(state, `config.models[${model}].name`, model)
  }
}

// actions
const actions = {
  async getCmsConfig ({ commit }) {
    const configResult = await Vue.prototype.$api.get('/config/cms')
    commit('setCms', configResult.result)
    let uiLang = LocalStorage.getItem('uiLang')
    if (!uiLang || uiLang === '') {
      uiLang = configResult.result.langs[0] || 'en'
    }
    commit('setUiLang', uiLang)
  }
}

// mutations
const mutations = {
  setConfig (state, newConfig) {
    _.set(newConfig, 'langs', _.get(newConfig, 'langs', ['en']))
    state.lang = newConfig.langs[0]
    for (const m in _.get(newConfig, 'models', [])) {
      for (const f in _.get(newConfig, `models[${m}].form`, [])) {
        const procesedComponents = []
        for (const c in _.get(newConfig, `models[${m}].form[${f}].components`, [])) {
          const component = _.get(newConfig, `models[${m}].form[${f}].components[${c}]`)
          if (_.startsWith(component.value, 'contents.')) {
            for (const l in newConfig.langs) {
              const langComponent = _.cloneDeep(component)
              langComponent.value += `.${newConfig.langs[l]}`
              langComponent.isMultiLang = true
              langComponent.props = {
                ...langComponent.props,
                lang: newConfig.langs[l],
                field: langComponent.value.split('.')[1]
              }
              procesedComponents.push(langComponent)
            }
          } else {
            component.isMultiLang = false
            procesedComponents.push(component)
          }
        }
        _.set(newConfig, `models[${m}].form[${f}].components`, procesedComponents)
      }
    }
    state.config = newConfig
  },
  setTitle (state, newTitle) {
    state.currentTitle = newTitle
  },
  setLang (state, newLang) {
    LocalStorage.set('lang', newLang)
    state.lang = newLang
  },
  setUiLang (state, newLang) {
    LocalStorage.set('uiLang', newLang)
    state.uiLang = newLang
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
