<template>
  <div id="q-app">
    <div v-if="!prepared" class="q-ma-xl flex justify-center">
      <q-spinner size="xl" />
    </div>
    <router-view v-if="prepared" />
  </div>
</template>

<script>
export default {
  name: 'Kusikusi',
  data () {
    return {
      prepared: false
    }
  },
  async created () {
    this.$api.baseURL = process.env.API_URL
    await this.$store.dispatch('getLocalSession')
    if (this.$store.getters.hasToken) {
      const meResult = await this.$api.get('/me')
      this.prepared = true
      if (meResult.status >= 400 && this.$route.name !== 'login') {
        this.$router.push({ name: 'login' })
      } else {
        this.$store.commit('setUser', meResult.data)
      }
    } else {
      this.prepared = true
      if (this.$route.name !== 'login') {
        this.$router.push({ name: 'login' })
      }
    }
  }
}
</script>
