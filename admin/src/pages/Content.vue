<template>
  <nq-page :title="entity.model" max-width="lg">
    <template slot="aside">
      <h2>{{ $t('content.publication') }}</h2>
      <q-card>
        <q-card-section class="row" v-if="!loading">
          <nq-field dense class="col-12" :readonly="!editing">
            <q-checkbox v-model="entity.is_active" :label="$t('content.active')" :disable="!editing" />
          </nq-field>
          <nq-input dense v-model="entity.view" :label="$t('content.view')" class="col-12" :readonly="!editing"/>
          <nq-input dense v-model="entity.published_at" :label="$t('content.publishedAt')" class="col-12" :readonly="!editing"/>
          <nq-input dense v-model="entity.unpublished_at" :label="$t('content.unpublishedAt')" class="col-12" :readonly="!editing"/>
        </q-card-section>
        <q-card-section v-if="loading">
          <q-skeleton type="QSlider" class="q-mb-md" />
          <q-skeleton type="QSlider" class="q-mb-md" />
          <q-skeleton type="QSlider" class="q-mb-md" />
          <q-skeleton type="QSlider" class="q-mb-md" />
        </q-card-section>
      </q-card>
    </template>
    <div v-for="(fieldset, fieldsetIndex) in fieldsets" :key="fieldsetIndex">
      <h2>{{ $t(fieldset.label) }}</h2>
      <q-card>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div v-for="(component, componentIndex) in fieldset.components"
                 :key="componentIndex"
                 :is="component.component"
                 :label="$t(component.label)"
                 :readonly="!editing"
                 :value="getValue(component)"
                 @input="setValue(component, $event)"
                 class="col-12" />
          </div>
        </q-card-section>
      </q-card>
    </div>
    <div class="fixed-top-right text-white action-buttons">
      <q-btn v-if="!editing && !loading"
             push size="lg" icon="edit" class="bg-accent no-border-radius"
             :label="$t('general.edit')"
             :disable="saving"
             @click="editing = true" />
      <q-btn v-if="editing"
             flat size="md" class="no-border-radius q-mr-sm"
             :label="$t('general.cancel')"
             @click="cancel" />
      <q-btn v-if="editing"
             push size="lg" icon="cloud_upload" class="bg-positive no-border-radius"
             :disable="loading"
             :loading="saving"
             @click="save"
             :label="$t('general.save')" />
    </div>
  </nq-page>
</template>

<script>
import _ from 'lodash'
import Children from '../components/Children'
export default {
  name: 'Content',
  components: { Children },
  data () {
    return {
      editing: false,
      loading: true,
      saving: false,
      fieldsets: [],
      entity: {
        id: '',
        model: '',
        view: '',
        entitycontents: [],
        entitiesrelated: [],
        parent_entity_id: '',
        is_active: true,
        properties: {},
        published_at: null,
        unpublished_at: null,
        version: 0,
        version_full: 0,
        version_relations: 0,
        version_tree: 0,
        created_at: null,
        updated_at: null,
        updated_by: null
      }
    }
  },
  async created () {
    await this.refreshEntity()
  },
  async beforeRouteUpdate (to, from, next) {
    await this.refreshEntity()
    next()
  },
  methods: {
    async refreshEntity () {
      this.loading = true
      this.editing = false
      this.saving = false
      this.fieldsets = []
      this.$store.commit('setEditButton', true)
      this.$store.commit('setSaveButton', false)
      this.entity.id = this.$route.params.entity_id || 'home'
      const contentResult = await this.$api.get('/entity/home?with=contents,entities_related')
      this.loading = false
      if (contentResult.success) {
        this.entity = contentResult.data
        /* const fieldsets = _.clone(_.get(this.$store.state, `ui.config.models.${this.entity.model}.form`, []))
        for (const f in fieldsets) {
          for (const c in fieldsets[f].components) {
            console.log(fieldsets[f].components[c].value)
            fieldsets[f].components[c].value = this.entity.model
          }
        } */
        this.fieldsets = _.get(this.$store.state, `ui.config.models.${this.entity.model}.form`, [])
      }
    },
    getValue (component) {
      if (component.isMultiLang) {
        const foundField = this.findContentRow(component.props)
        if (foundField) {
          return foundField.text
        } else {
          this.entity.contents.push({
            lang: component.props.lang,
            field: component.props.field,
            text: ''
          })
        }
      } else {
        return this.entity[component.value]
      }
    },
    setValue (component, value) {
      if (component.isMultiLang) {
        const foundField = this.findContentRow(component.props)
        if (foundField) {
          foundField.text = value
        }
      } else {
        this.entity[component.value] = value
      }
    },
    findContentRow (props) {
      return _.find(this.entity.contents, (o) => { return o.lang === props.lang && o.field === props.field })
    },
    cancel () {
      this.refreshEntity()
    },
    async save () {
      if (this.entity.id === 'new') {

      } else {
        const updateResult = await this.$api.patch(`/entity/${this.entity.id}`, this.entity)
        if (updateResult.success) {
          this.$q.notify({
            position: 'top',
            color: 'positive',
            message: this.$t('content.saveOk')
          })
          this.editing = false
        } else {
          this.$q.notify({
            position: 'top',
            color: 'negative',
            message: this.$t('login.saveError')
          })
        }
      }
    }
  }
}
</script>
<style lang="scss">
  .action-buttons {
    z-index: 2001;
    .q-btn {
       max-height: 50px
    }
  }
</style>
