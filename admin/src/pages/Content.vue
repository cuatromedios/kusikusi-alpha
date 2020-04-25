<template>
  <nq-page title="Contenido">

  </nq-page>
</template>

<script>
export default {
  name: 'Content',
  data () {
    return {
      entity: {
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
      }
    }
  },
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
