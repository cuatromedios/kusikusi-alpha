<template>
  <div v-show="readonly">
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
      <draggable v-model="children" :options="{ animation: 500, sort: true }" :disabled="!reorderMode">
          <q-item
              v-for="entity in children"
              class="child-item"
              :class="{ 'cursor-drag': reorderMode }"
              :key="entity.id">
            <q-item-section avatar top>
              <div class="flex items-center item-avatar">
                <q-icon name="drag_indicator" size="md" color="grey" v-if="reorderMode" />
                <q-avatar :icon="$store.getters.iconOf(entity.model)" color="grey" text-color="white" />
              </div>
            </q-item-section>
            <q-item-section>
              <q-item-label>
                <h3>
                  <span v-if="reorderMode">{{ entity.title }}</span>
                  <router-link v-if="!reorderMode" :to="{ name: 'content', params: { entity_id:entity.id } }">{{ entity.title }}</router-link>
                </h3>
              </q-item-label>
              <q-item-label caption lines="1">{{ $t($store.getters.nameOf(entity.model)) }}</q-item-label>
            </q-item-section>
            <q-item-section side>
            </q-item-section>
          </q-item>
      </draggable>
    </q-list>
    <q-btn v-if="canReorder && !reorderMode" flat icon="swap_vert" :label="$t('contents.reorder')" class="q-mt-sm" @click="startReorder" />
    <q-btn v-if="reorderMode" :loading="reordering" :disable="reordering" flat icon="done" :label="$t('general.confirm')" class="q-mt-sm" color="positive" @click="reorder" />
    <q-btn v-if="reorderMode" flat :label="$t('general.cancel')" class="q-mt-sm" color="grey" @click="cancelReorder" />
  </div>
</template>
<script>
import draggable from 'vuedraggable'
import _ from 'lodash'
export default {
  components: { draggable },
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
      const childrenResult = await this.$api.get(`/entities?child-of=${this.entity.id}&select=contents.title,published_at,unpublished_at,is_active,model,id,relation_children.relation_id&only-published=false&order-by=${this.order_by}`)
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
