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
    <q-list bordered separator>
      <draggable v-model="children">
        <q-item
            v-for="entity in children"
            :key="entity.id"
            :tags="tags"
        />
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
    console.log('Children')
  },
  methods: {
  }
}
</script>

<style lang="stylus">
</style>
