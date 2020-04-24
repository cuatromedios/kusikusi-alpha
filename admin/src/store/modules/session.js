import { LocalStorage } from 'quasar'
import _ from 'lodash'

// initial state
const state = {
  user: {
    profile: ''
  },
  authtoken: ''
}

// getters
const getters = {
  hasAuthtoken (state) {
    return (state.authtoken !== '')
  },
  entitiesWithWritePermissions (state) {
    let entities = []
    for (let e = 0; e < _.get(state, 'user.permissions.length', 0); e++) {
      if (state.user.permissions[e].write !== 'none' && state.user.permissions[e].read !== 'none') {
        let entity = state.user.permissions[e].entity
        entity.write = state.user.permissions[e].write
        entity.read = state.user.permissions[e].read
        entities.push(entity)
      }
    }
    return entities
  },
  entitiesWithPermissions (state) {
    let entities = []
    for (let e = 0; e < _.get(state, 'user.permissions.length', 0); e++) {
      if (state.user.permissions[e].read !== 'none') {
        let entity = state.user.permissions[e].entity
        entity.write = state.user.permissions[e].write
        entity.read = state.user.permissions[e].read
        entities.push(entity)
      }
    }
    return entities
  }
}

// actions
const actions = {
  getLocalSession ({ dispatch, commit }) {
    const session = LocalStorage.getItem('session')
    if (!session || session === {}) {
      dispatch('resetUserData')
    } else {
      commit('setAuthtoken', session.authtoken)
      commit('setUser', session.user)
    }
    return session
  },
  resetUserData ({ commit }) {
    commit('setAuthtoken', '')
    commit('setUser', {})
  }
}

// mutations
const mutations = {
  setAuthtoken (state, newToken) {
    state.authtoken = newToken
    // Api.setHeader('Authorization', 'Bearer ' + newToken)
    let session = LocalStorage.getItem('session') || {}
    session.authtoken = newToken
    LocalStorage.set('session', session)
  },
  setUser (state, newUser) {
    state.user = newUser
    let session = LocalStorage.getItem('session') || {}
    session.user = newUser
    LocalStorage.set('session', session)
  }
}

export default {
  namespaced: false,
  state,
  getters,
  actions,
  mutations
}
