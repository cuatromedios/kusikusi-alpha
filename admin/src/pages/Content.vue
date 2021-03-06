<template>
  <nq-page :title="getEntityTitle" max-width="lg">
    <template slot="before" v-if="ancestors.length > 0">
      <q-breadcrumbs>
        <q-breadcrumbs-el v-for="ancestor in ancestors"
                          :key="ancestor.id"
                          :label="ancestor.title"
                          :to="{ name: 'content', params: { entity_id: ancestor.id } }"
        />
        <q-breadcrumbs-el />
      </q-breadcrumbs>
    </template>
    <div id="langSelector" v-if="$store.state.ui.config && $store.state.ui.config.langs.length > 1" class="rounded-borders shadow-1">
      <q-radio v-model="contentLang"
               size="sm"
               v-for="lang in $store.state.ui.config.langs"
               :key="lang"
               :val="lang"
               :label="$t(lang)"
      />
      <q-radio v-model="contentLang"
               val="all"
               :label="$t('all')"/>
    </div>
    <template slot="aside">
      <h2>{{ $t('contents.publication') }}</h2>
      <q-card>
        <q-card-section  v-if="!loading">
          <div class="row q-col-gutter-sm">
            <nq-field dense class="col-12" :readonly="!editing">
              <q-checkbox v-model="entity.is_active" :label="$t('contents.active')" :disable="!editing" left-label color="dark" />
            </nq-field>
            <nq-select dense v-model="entity.view" :label="$t('contents.view')" class="col-12" :readonly="!editing" :options="views"/>
            <nq-date-time dense v-model="entity.published_at" display-format="dddd DD MMMM YYYY, h:mm a" :label="$t('contents.publishedAt')" class="col-12" :readonly="!editing" v-if="editing"></nq-date-time>
            <nq-field :label="$t('contents.publishedAt')" class="col-12" readonly stack-label v-if="!editing">
              <p>{{ entity.published_at | moment('dddd DD MMMM YYYY, h:mm a') }}
                <small>(UTC&nbsp;{{ entity.published_at | moment('Z') }})</small></p>
            </nq-field>
            <nq-field label="ID" class="col-12" readonly stack-label>
              <p>{{ entity.id }}</p>
            </nq-field>
          </div>
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
                 :label="`${$t(component.label)} ${component.isMultiLang ? '('+component.props.lang+')' : ''}`"
                 :readonly="!editing"
                 :data-multilingual="component.isMultiLang"
                 :data-lang="component.props ? component.props.lang : ''"
                 :lang="component.props ? component.props.lang : null"
                 :entity="entity"
                 v-show="!component.isMultiLang || (component.isMultiLang && (component.props.lang === contentLang || contentLang === 'all'))"
                 :value="getValue(component)"
                 @input="setValue(component, $event)"
                 v-bind="component.props"
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
             :disable="loading || saving"
             :loading="saving"
             @click="save"
             :label="$t('general.save')" />
    </div>
  </nq-page>
</template>

<script>
import _ from 'lodash'
import moment from 'moment'
import Children from '../components/Children'
import Media from '../components/Media'
import HtmlEditor from '../components/HtmlEditor'
import Slug from '../components/Slug'
export default {
  name: 'Content',
  components: { Children, Media, HtmlEditor, Slug },
  data () {
    return {
      editing: false,
      loading: true,
      saving: false,
      fieldsets: [],
      ancestors: [],
      entity: {
        id: '',
        model: '',
        view: '',
        contents: [],
        entity_relations: [],
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
    await this.refreshEntity(this.$route.params.entity_id)
  },
  async beforeRouteUpdate (to, from, next) {
    await this.refreshEntity(to.params.entity_id, to.params.model, to.params.parent_entity_id)
    next()
  },
  methods: {
    async refreshEntity (entity_id, model, parent_entity_id) {
      this.loading = true
      this.editing = false
      this.saving = false
      this.fieldsets = []
      this.$store.commit('setEditButton', true)
      this.$store.commit('setSaveButton', false)
      this.entity.id = entity_id || 'home'
      if (entity_id !== 'new') {
        const contentResult = await this.$api.get(`/entity/${this.entity.id}?with=contents,entities_related`)
        this.loading = false
        if (contentResult.success) {
          this.entity = contentResult.data
          const ancestorsResult = await this.$api.get(`/entities/?ancestor-of=${this.entity.id}&descendant-of=root&select=id,contents.title&order-by=ancestor_relation_depth:desc`)
          if (ancestorsResult.success) {
            this.ancestors = ancestorsResult.data.data
          } else {
            this.ancestors = []
          }
        } else {
          this.entity = {
            contents: [],
            entity_relations: []
          }
        }
      } else {
        this.entity = _.cloneDeep(this.$store.state.content.blankEntity)
        this.entity.model = model || this.$route.params.model
        this.entity.view = model || this.$route.params.model
        this.entity.id = entity_id || this.$route.params.entity_id
        this.entity.parent_entity_id = parent_entity_id || this.$route.params.parent_entity_id
        this.entity.published_at = moment().format()
        this.entity.unpublished_at = null
        this.editing = true
        this.loading = false
      }
      this.fieldsets = _.get(this.$store.state, `ui.config.models.${this.entity.model}.form`, [])
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
      if (!this.entity) return undefined
      return _.find(this.entity.contents, (o) => { return o.lang === props.lang && o.field === props.field })
    },
    cancel () {
      if (this.isNew) {
        this.$router.replace({ name: 'content', params: { entity_id: this.$route.params.parent_entity_id } })
      } else {
        this.refreshEntity(this.entity.id)
      }
    },
    async save () {
      this.saving = true
      let saveResult
      if (this.isNew) {
        delete this.entity.id
        saveResult = await this.$api.post('/entity', this.entity)
      } else {
        saveResult = await this.$api.patch(`/entity/${this.entity.id}`, this.entity)
      }
      this.saving = false
      if (saveResult.success) {
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
          message: this.$t('general.saveError'),
          caption: `${saveResult.data.message} (${saveResult.status})`
        })
      }
    }
  },
  computed: {
    getEntityTitle () {
      if (!this.entity) return ''
      const fromContents = this.findContentRow({ lang: this.$store.state.ui.config.langs[0], field: 'title' })
      return fromContents && fromContents.text !== '' ? fromContents.text : this.$store.getters.nameOf(this.entity.model)
    },
    isNew () {
      return this.$route.params.entity_id === 'new'
    },
    views () {
      return _.get(this.$store.state, `ui.config.models.${this.entity.model}.views`, [this.entity.model])
    },
    contentLang: {
      set (lang) {
        this.$store.commit('setLang', lang)
      },
      get () {
        return this.$store.state.ui.lang
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
  #langSelector {
    font-size: 0.85em;
    position: fixed;
    top: 64px;
    right: 24px;
    background-color: rgba(255,255,255,0.5);
    padding: 4px 16px;
  }
</style>
