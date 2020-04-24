<template>
  <main class="q-pb-xl">
    <q-card flat bordered class="my-card q-mb-lg entity-card" v-if="!ready">
      <q-card-section>
        <q-banner dense rounded class="bg-grey-2 q-mb-xs placeholder ph-subtitle"></q-banner>
        <q-banner rounded class="bg-grey-4 q-mb-xs placeholder ph-title"></q-banner>
        <q-banner dense rounded class="bg-grey-2 placeholder ph-summary"></q-banner>
      </q-card-section>
      <div class="fade-screen"></div>
    </q-card>
    <div v-if="ready">
      <q-breadcrumbs class="q-mb-sm q-mt-none">
        <q-breadcrumbs-el v-for="ancestor in $store.getters['content/getBreadcrums']"
                          :key="ancestor.id"
                          :to="{name: 'contentDisplay', params: {entity_id: ancestor.id}}"
                          :icon="ancestor.icon"
                          :label="ancestor.title" />
      </q-breadcrumbs>
      <div v-for="item in $store.state.ui.config.models[$store.state.content.entity.model].display"
           v-bind:key="item.index"
           :is="item.component"
           :settings="item.settings"
           v-bind="item.props"
           class="q-mb-lg">
      </div>
    </div>
  </main>
</template>
<script>
export default {
  components: { },
  name: 'ContentDisplay',
  data () {
    return {
      ready: false,
      entityId: undefined
    }
  },
  props: ['editBus'],
  computed: {
    title () {
      return this.$t('content.title')
    }
  },
  meta () {
    return {
      title: this.title
    }
  },
  async mounted () {
    await this.getEntity()
  },
  beforeDestroy () {
    this.removeEditButton()
  },
  watch: {
    '$route' (to, from) {
      this.getEntity()
    }
  },
  methods: {
    async getEntity () {
      this.ready = false
      this.removeEditButton()
      this.entityId = this.$route.params.entity_id || 'home'
      await this.$store.dispatch('content/getEntity', this.entityId)
      this.addEditButton()
      this.ready = true
    },
    edit () {
      this.$router.push({ name: 'contentEdit', params: { entity_id: this.entityId } })
    },
    addEditButton () {
      this.editBus.$on('on-edit', this.edit)
      this.$store.commit('ui/setEditButton', this._.get(this.$store, 'state.ui.config.models[' + this.$store.state.content.entity.model + '].editor.length') > 0)
    },
    removeEditButton () {
      this.editBus.$off()
      this.$store.commit('ui/setEditButton', false)
    }
  }
}
</script>
<style>
</style>
