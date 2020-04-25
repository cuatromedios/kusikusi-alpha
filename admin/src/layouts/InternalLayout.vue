<template>
  <q-layout view="hHh lpR lFf" class="bg-grey-3">
    <q-header class="bg-primary text-white">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="left = !left"/>
        <q-toolbar-title>
          <q-avatar>
            <img src="~assets/logo.svg">
          </q-avatar>
          Kusikusi
        </q-toolbar-title>
        <q-btn v-if="$store.state.ui.toolbar.editButton"
               flat size="lg" icon="edit" type="a" class="bg-accent no-border-radius action-button last-action-button"
               :label="$t('general.edit')"
               @click="editBus.$emit('on-edit', {})" />
        <q-btn v-if="$store.state.ui.toolbar.saveButton"
               flat size="md" type="a" class="no-border-radius q-mr-sm"
               :label="$t('general.cancel')"
               @click="saveBus.$emit('on-cancel', {})" />
        <q-btn v-if="$store.state.ui.toolbar.saveButton"
               flat size="lg" icon="cloud_upload" type="a" class="bg-positive no-border-radius action-button last-action-button"
               @click="saveBus.$emit('on-save', {})"
               :label="$t('general.save')" />
      </q-toolbar>
    </q-header>
    <q-drawer v-model="left"
              side="left"
              bordered
              :mini="miniState"
              @mouseover="miniState = false"
              @mouseout="miniState = true">
      <q-list class="rounded-borders text-info q-mt-lg">
        <q-item
          v-for="item in $store.getters['menu']"
          :key="item.label"
          clickable
          v-ripple
          :active="false"
          active-class="bg-info text-white"
          :to="{ name: item.name, params: item.params} ">
          <q-item-section avatar>
            <q-icon :name="item.icon"/>
          </q-item-section>
          <q-item-section>{{ $t(item.label) }}</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>
    <q-page-container>
        <router-view :editBus="editBus" :saveBus="saveBus"/>
    </q-page-container>
  </q-layout>
</template>

<script>
import Vue from 'vue'
export default {
  name: 'InternalLayout',
  data () {
    return {
      left: true,
      miniState: true,
      editBus: new Vue(),
      saveBus: new Vue()
    }
  }
}
</script>

<style lang="stylus">
  .action-button
    margin: 0
    &.last-action-button
      margin 0 -12px 0 0
</style>
