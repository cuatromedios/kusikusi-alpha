<template>
  <nq-page title="Contenido">

  </nq-page>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { Entity } from '../models'

@Component
export default class ClassComponent extends Vue {
  private entity: Entity = {
    id: '',
    model: '',
    view: '',
    entityContents: [],
    entitiesRelated: [],
    parent_entity_id: '',
    is_active: true,
    properties: {},
    published_at: '',
    unpublished_at: '',
    version: 0
  };

  async mounted () {
    console.log(this.$route.name, this.$route.params.entity_id)
    this.entity.id = this.$route.params.entity_id || 'home'
    const contentResult = await this.$api.get('/entity/home?with=entityContents,entitiesRelated')
    if (contentResult.success) {
      this.entity = contentResult.data
    }
  }
}
</script>
