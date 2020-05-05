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
      <q-list bordered>
        <draggable v-model="children" v-bind="getDragOptions()" :disabled="!reorderMode">
          <child-item v-for="child in children"
                      :key="child.id"
                      :child="child"
                      :entity_id="entity.id"
                      :tags="tags"
                      :reorderMode="reorderMode"
          />
        </draggable>
      </q-list>
      <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
      <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
      <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
    </div>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
import ChildItem from './ChildItem'
export default {
  components: { ChildItem, draggable },
  name: 'Children',
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
      children: [],
      storedChildren: [],
      reorderMode: false,
      reordering: false
    }
  },
  mounted () {
    this.getChildren()
  },
  methods: {
    async getChildren () {
      this.loading = true
      const childrenResult = await this.$api.get(`/entities?child-of=${this.entity.id}&select=contents.title,published_at,unpublished_at,is_active,model,id,relation_children.relation_id&only-published=false&order-by=${this.order_by}`)
      this.loading = false
      if (childrenResult.success) {
        this.children = childrenResult.data.data
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
      this.storedChildren = _.cloneDeep(this.children)
      this.reorderMode = true
    },
    cancelReorder () {
      this.children = _.cloneDeep(this.storedChildren)
      this.reorderMode = false
    },
    async reorder () {
      this.reordering = true
      const relation_ids = _.flatMap(this.children, (c) => c.relation_id)
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
      return this.order_by === 'relation_children.position'
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
