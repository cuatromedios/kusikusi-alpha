<template>
  <div v-show="readonly">
    <div v-if="loading" class="flex column items-end">
        <q-skeleton type="QBtn" class="q-mb-md" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
        <q-skeleton type="QInput" class="q-mb-md full-width" />
    </div>
    <div v-if="!loading">
      <div class="q-mb-md flex justify-end">
        <q-btn-dropdown class="" outline color="positive"  icon="add_circle"  :label="$t('general.add')" v-if="models && models.length > 1">
          <q-list>
            <q-item clickable v-close-popup
                    v-for="model in models"
                    @click="add(model)"
                    :key="model">
              <q-item-section>
                <q-item-label>{{ $t($store.getters.nameOf(model)) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn class=""
               outline color="positive"  icon="add_circle"
               :label="`${$t('general.add')} ${$t($store.getters.nameOf(models[0]))}`"
               v-if="models && models.length === 1"
               @click="add(models[0])"/>
      </div>
      <draggable v-model="media" v-bind="getDragOptions()" :disabled="!reorderMode" class="row draggable-container q-col-gutter-md">
        <div v-for="medium in media"
             :key="medium.id"
             class="col-4">
          <medium-item :medium="medium"
                       :entity_id="entity.id"
                       :tags="tags"
                       :reorderMode="reorderMode"
                       class="full-width full-height"
          />
        </div>
      </draggable>
      <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
      <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
      <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
    </div>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
import MediumItem from './MediumItem'
export default {
  components: { MediumItem, draggable },
  name: 'Media',
  props: {
    entity: {},
    readonly: {
      type: Boolean,
      default: true
    },
    ofModel: {
      type: Array,
      default: () => []
    },
    models: {
      type: Array,
      default: () => []
    },
    tags: {
      type: Array,
      default: () => []
    },
    order_by: {
      type: String,
      default: 'relation_children.position'
    }
  },
  data () {
    return {
      loading: true,
      media: [],
      storedMedia: [],
      reorderMode: false,
      reordering: false
    }
  },
  mounted () {
    this.getMedia()
  },
  methods: {
    async getMedia () {
      this.loading = true
      const mediaResult = await this.$api.get(`/entities/medium?media-of=${this.entity.id}&select=contents.title,properties,is_active,model,id,relation_media.relation_id&only-published=false&order-by=relation_media.position`)
      this.loading = false
      if (mediaResult.success) {
        this.media = mediaResult.data.data
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
    },
    add (model) {
      this.$router.push({ name: 'content', params: { entity_id: 'new', model: model, conector: 'in', parent_entity_id: this.entity.id } })
    },
    getDragOptions () {
      return { animation: 500, sort: true }
    },
    startReorder () {
      this.storedMedia = _.cloneDeep(this.media)
      this.reorderMode = true
    },
    cancelReorder () {
      this.media = _.cloneDeep(this.storedMedia)
      this.reorderMode = false
    },
    async reorder () {
      this.reordering = true
      const relation_ids = _.flatMap(this.media, (c) => c.relation_id)
      const reorderResult = await this.$api.patch('/entities/relations/reorder', { relation_ids })
      this.reordering = false
      if (reorderResult.success) {
        this.reorderMode = false
      } else {
        this.$q.notify({
          position: 'top',
          color: 'negative',
          message: this.$t('general.serverError')
        })
      }
    }
  },
  computed: {
    canReorder () {
      return this.media.length > 1
    }
  }
}
</script>

<style lang="scss">
  .cursor-drag {
    cursor: grab;
  }
  .sortable-chosen {
    outline: 1px solid lightgrey;
    background-color: white;
  }
  .sortable-ghost {
    cursor: grab;
  }
  .children-move {
    transition: transform 0.5s;
  }
</style>
