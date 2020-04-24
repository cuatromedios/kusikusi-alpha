<template>
  <transition
    enter-active-class="animated shake"
    leave-active-class="animated fadeOut"
    appear >
    <q-banner inline-actions
              class="text-white bg-negative"
              v-if="message !== ''">
      {{ message }}
    </q-banner>
  </transition>
</template>
<script>
export default {
  components: {},
  name: 'Feedback',
  data () {
    return {
    }
  },
  props: {
    type: {
      type: String,
      default: 'negative'
    },
    message: {
      type: String,
      default: ''
    },
    dismissText: {
      type: String,
      default: ''
    }
  }
}
</script>
<style>

</style>
