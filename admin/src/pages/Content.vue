<template>
  <nq-page :title="entity.model" max-width="lg">
    <template slot="aside">
      <h2>{{ $t('content.publication') }}</h2>
      <q-card>
        <q-card-section class="row q-col-gutter-sm">
          <nq-field dense class="col-12" :readonly="!editing">
            <q-checkbox v-model="entity.is_active" :label="$t('content.active')" :disable="!editing" />
          </nq-field>
          <nq-input dense v-model="entity.view" :label="$t('content.view')" class="col-12" :readonly="!editing"/>
          <nq-input dense v-model="entity.published_at" :label="$t('content.publishedAt')" class="col-12" :readonly="!editing"/>
          <nq-input dense v-model="entity.unpublished_at" :label="$t('content.unpublishedAt')" class="col-12" :readonly="!editing"/>
        </q-card-section>
      </q-card>
    </template>
    <h2>{{ $t('content.contents') }}</h2>
    <p>Nunc finibus odio nunc, id pellentesque lacus tempor semper....</p>
  </nq-page>
</template>

<script>
export default {
  name: 'Content',
  data () {
    return {
      editing: false,
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
  async mounted () {
    this.$store.commit('setEditButton', true)
    this.$store.commit('setSaveButton', false)
    this.entity.id = this.$route.params.entity_id || 'home'
    const contentResult = await this.$api.get('/entity/home?with=contents,entities_related')
    if (contentResult.success) {
      this.entity = contentResult.data
    }
  }
}
</script>
