<template>
  <q-card
    class="medium-item"
    :class="{ 'cursor-drag': reorderMode }">
    <q-img :src="`${$store.getters.media_url}${medium.thumb}`"
           :ratio="1" contain
           v-if="medium.properties && medium.properties.isWebImage"
           class="bg-grey-2" />
    <q-responsive :ratio="1" v-if="!medium.properties || !medium.properties.isWebImage" >
      <div class="rounded-borders bg-grey-2 text-white flex flex-center">
        <q-icon size="80px"
                color="grey-5"
                :name="medium.properties.isImage ? 'image' : medium.properties.isVideo ? 'movie' : medium.properties.isAudio ? 'graphic_eq' : medium.properties.isDocument ? 'description' : 'insert_drive_file'"
                v-if="!medium.properties || !medium.properties.isWebImage" />
      </div>
    </q-responsive>
    <q-btn-dropdown round dropdown-icon="more_vert" flat color="grey-6" class="absolute-top-right">
      <q-list>
        <q-item clickable v-close-popup :to="{ name: 'content', params: { entity_id: medium.id } }" target="_blank">
          <q-item-section avatar>
            <q-icon name="launch" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('media.edit') }}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="onUnlink">
          <q-item-section avatar>
            <q-icon name="link_off" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ $t('media.unlink') }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
    <q-separator/>
    <q-card-actions side v-if="tags && tags.length > 0" class="row">
      <h3 class="col-12" style="word-break: break-all">
        <q-icon :name="medium.properties.isImage ? 'image' : medium.properties.isVideo ? 'movie' : medium.properties.isAudio ? 'graphic_eq' : medium.properties.isDocument ? 'description' : 'insert_drive_file'" class="q-mr-xs" />
        {{ medium.title || $t($store.getters.nameOf(medium.model))}}
        <span v-if="medium.properties" class="text-grey-8">({{ medium.properties.format }})</span>
      </h3>
      <div class="row items-center col-12">
        <q-select v-model="editingTags"
                  @input="onInput"
                  :options="tags"
                  multiple use-chips
                  ref="tagSelector"
                  dense borderless hide-dropdown-icon>
          <template v-slot:prepend>
            <q-icon name="local_offer" size="xs" color="info" />
          </template>
        </q-select>
        <q-btn dense outline rounded icon="check" size="sm" color="positive" v-if="editing" :loading="saving" @click="acceptTags">{{ $t('general.confirm') }}&nbsp;&nbsp;</q-btn>
        <q-btn dense flat rounded size="sm" color="grey" v-if="editing" @click="cancelTags">{{ $t('general.cancel') }}</q-btn>
      </div>
    </q-card-actions>
  </q-card>
</template>
<script>
import _ from 'lodash'
export default {
  name: 'MediaItem',
  props: ['medium', 'tags', 'reorderMode', 'entity_id'],
  data () {
    return {
      saving: false,
      editing: false,
      editingTags: [],
      storedTags: []
    }
  },
  mounted () {
    this.editingTags = _.clone(this.medium.media_tags)
    this.storedTags = _.clone(this.medium.media_tags)
  },
  methods: {
    onInput () {
      this.$refs.tagSelector.hidePopup()
      this.editing = true
    },
    async acceptTags () {
      this.$refs.tagSelector.hidePopup()
      this.saving = true
      this.storedTags = _.clone(this.editingTags)
      await this.$api.post(`/entity/${this.entity_id}/relation`, {
        called_entity_id: this.medium.id,
        kind: 'medium',
        tags: this.storedTags,
        position: this.medium.media_position,
        depth: 0
      })
      this.saving = false
      this.editing = false
    },
    cancelTags () {
      this.$refs.tagSelector.hidePopup()
      this.editingTags = _.clone(this.storedTags)
      this.editing = false
      this.saving = false
    },
    onUnlink () {
      this.$q.dialog({
        title: this.$t('media.unlink'),
        ok: {
          label: this.$t('general.ok'),
          color: 'primary'
        },
        cancel: {
          label: this.$t('general.cancel'),
          color: 'grey',
          flat: true
        },
        message: this.$t('media.unlinkConfirm')
      }).onOk(async () => {
        await this.$api.delete(`/entity/${this.entity_id}/relation/${this.medium.id}/medium`)
        this.$emit('getMedia')
      })
    }
  }
}
</script>
