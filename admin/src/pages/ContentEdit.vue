<template>
  <main class="q-pb-xl">
    <lang-tabs v-if="ready" />
    <div v-if="ready">
      <q-card flat bordered class="q-mb-sm"
              v-for="group in $store.state.ui.config.models[$store.state.content.entity.model].editor"
              v-bind:key="group.index"
              v-bind="group.props"
              >
        <q-card-section>
          <h2 class="q-mb-sm">{{ $t(group.groupName) }}</h2>
          <field-wrapper v-for="field in group.fields"
               v-bind:key="field.index"
               :label="field.label"
               :field="field.field"
               :component="field.component"
               :props="field.props"
               >
          </field-wrapper>
        </q-card-section>
      </q-card>
    </div>
    <div class="text-center">
      <q-btn v-if="ready && $store.state.content.entity.id !== 'new'"
             flat color="negative" size="xs" class="q-mt-md"
             @click="deleteEntity" >
        {{ $t('general.delete') }}
      </q-btn>
      <q-btn v-if="_.get($store, 'state.content.entity.id') === 'new' && _.get($store, 'state.session.user.profile') === 'admin'"
             flat size="xs" class="q-mt-md"
             color="grey-5"
             @click="showCustomId = true">
        ID
      </q-btn>
      <q-input v-if="showCustomId"
               v-model="customId"
               outlined dense autofocus
               mask='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
               label="Custom ID"></q-input>
    </div>
  </main>
</template>
<script>
import FieldWrapper from '../components/FieldWrapper'
import LangTabs from '../components/LangTabs'
export default {
  components: { FieldWrapper, LangTabs },
  name: 'ContentEdit',
  data () {
    return {
      showLangMenu: true,
      ready: false,
      customId: '',
      showCustomId: false
    }
  },
  props: ['saveBus'],
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
  beforeDestroy () {
    this.removeSaveButton()
  },
  async mounted () {
    this.removeSaveButton()
    if (this.$store.state.ui.config.models === undefined) {
      this.$store.watch(
        (state) => state.ui.config.models,
        (newValue, oldValue) => {
          if (newValue) {
            this.getEntity()
          }
        }
      )
    } else {
      this.getEntity()
    }
  },
  watch: {
  },
  methods: {
    async getEntity () {
      this.removeSaveButton()
      let entityId = this.$route.params.entity_id
      if (entityId === 'new') {
        await this.$store.dispatch('content/newEntity', { model: this.$route.params.model, parent_id: this.$route.params.parent_id })
      } else if (entityId !== this.$store.state.content.entity.id) {
        await this.$store.dispatch('content/getEntity', entityId)
      }
      this.ready = true
      this.addSaveButton()
    },
    async saveEntity () {
      let call
      if (this.$store.state.ui.config.langs[0] && this.$store.state.content.entity.contents[this.$store.state.ui.config.langs[0]] && this.$store.state.content.entity.contents[this.$store.state.ui.config.langs[0]].title) {
        this.$store.commit('content/setEntityValue', {
          field: 'name',
          value: this.$store.state.content.entity.contents[this.$store.state.ui.config.langs[0]].title
        })
      }
      if (this.$route.params.entity_id === 'new') {
        this.$store.dispatch('content/clearId')
        if (this.customId !== '') {
          this.$store.state.content.entity.id = this.customId
        }
        call = await this.$api.post(`/entity`, this.$store.state.content.entity)
        if (call.success) {
          this.$router.push({ name: 'contentDisplay', params: { entity_id: call.result.id } })
        }
      } else {
        call = await this.$api.patch(`/entity/${this.$route.params.entity_id}`, this.$store.state.content.entity)
        if (call.success) {
          this.$router.push({ name: 'contentDisplay', params: { entity_id: this.$route.params.entity_id } })
        }
      }
    },
    async cancelEdit () {
      if (this.$route.params.entity_id === 'new') {
        this.$router.push({ name: 'contentDisplay', params: { entity_id: this.$route.params.parent_id } })
      } else {
        this.$router.push({ name: 'contentDisplay', params: { entity_id: this.$route.params.entity_id } })
      }
    },
    async deleteEntity () {
      this.$q.dialog({
        title: this.$t('general.sure'),
        message: this.$t('content.editor.delete'),
        color: 'negative',
        cancel: {
          flat: true,
          label: this.$t('general.cancel'),
          color: 'grey'
        },
        ok: {
          color: 'negative',
          label: this.$t('general.delete'),
          icon: 'delete'
        },
        persistent: true
      }).onOk(async () => {
        await this.$api.delete(`/entity/${this.$route.params.entity_id}`)
        this.$router.push({ name: 'contentDisplay', params: { entity_id: this.$store.state.content.entity.parent_id } })
      })
    },
    addSaveButton () {
      this.saveBus.$on('on-save', this.saveEntity)
      this.saveBus.$on('on-cancel', this.cancelEdit)
      this.$store.commit('ui/setSaveButton', true)
    },
    removeSaveButton () {
      this.saveBus.$off()
      this.$store.commit('ui/setSaveButton', false)
    }
  }
}
</script>
<style>
</style>
