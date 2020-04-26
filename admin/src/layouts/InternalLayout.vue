<template>
  <q-layout view="hHh lpR lFf" class="bg-grey-3">
    <q-header class="bg-primary text-white">
      <q-toolbar>
        <q-btn dense icon="menu" flat @click="left = !left">
          <img src="~assets/logo.svg" style="width: 2.5em;" >
        </q-btn>
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
</style>
