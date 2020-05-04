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
              <q-item-label>{{ $store.getters.nameOf(model) }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-btn-dropdown>
      <q-btn class=""
             outline color="positive"  icon="add_circle"
             :label="`${$t('general.add')} ${$store.getters.nameOf(models[0])}`" v-if="models && models.length === 1"
             @click="add(models[0])"/>
    </div>
    <q-list bordered>
      <draggable v-model="children">
        <q-item
            v-for="entity in children"
            :key="entity.id">
          <q-item-section avatar top>
            <q-avatar :icon="$store.getters.iconOf(entity.model)" color="grey" text-color="white" />
          </q-item-section>

          <q-item-section>
            <q-item-label><h3><router-link :to="{ name: 'content', params: { entity_id:entity.id } }">{{ entity.title }}</router-link></h3></q-item-label>
            <q-item-label caption lines="1">{{ $store.getters.nameOf(entity.model) }}</q-item-label>
          </q-item-section>
          <q-item-section side>
          </q-item-section>
        </q-item>
      </draggable>
    </q-list>
  </div>
</template>
<script>
import draggable from 'vuedraggable'
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
      default: 'child_relation_position'
    }

  },
  data () {
    return {
      children: []
    }
  },
  mounted () {
    this.getChildren()
  },
  methods: {
    async getChildren () {
      const childrenResult = await this.$api.get(`/entities?child-of=${this.entity.id}&select=contents.title,published_at,unpublished_at,is_active,model,id&only-published=false&order-by=${this.order_by}`)
      this.children = childrenResult.data.data
    },
    add (model) {
      this.$router.push({ name: 'content', params: { entity_id: 'new', model: model, conector: 'in', parent_entity_id: this.entity.id } })
    }
  }
}
</script>

<style lang="stylus">
</style>
