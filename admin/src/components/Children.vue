<template>
  <div>
    <!--q-btn-dropdown class="absolute-top-right q-ma-md" outline color="positive"  icon="add_circle"  :label="$t('general.add')" v-if="allowed && allowed.length > 1">
      <q-list>
        <q-item clickable v-close-popup
                v-for="model in allowed"
                @click="$router.push({name: 'contentNew', params: {entity_id: 'new', parent_id: $store.state.content.entity.id, model: model}})"
                :key="model">
          <q-item-section>
            <q-item-label>{{ $store.state.ui.config.models[model] ? $store.state.ui.config.models[model].name : model }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
    <q-btn class="absolute-top-right q-ma-md"
           outline color="positive"  icon="add_circle"
           :label="`${$t('general.add')} ${$store.state.ui.config.models[allowed[0]].name}`" v-if="allowed && allowed.length === 1"
           @click="$router.push({name: 'contentNew', params: {entity_id: 'new', parent_id: $store.state.content.entity.id, model: allowed[0]}})"
    /-->
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
    entity: {
      type: Object,
      default: () => {}
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
    async getChildren (page = 1) {
      const childrenResult = await this.$api.get(`/entities?child-of=${this.entity.id}&select=contents.title,published_at,unpublished_at,is_active,model,id`)
      this.children = childrenResult.data.data
    }
  }
}
</script>

<style lang="stylus">
</style>
