/**
 * Base mixin for field components.
 */
export const nqFieldMixin = {
  data () {
    return {
      focused: false
    }
  },
  mounted () {
  },
  methods: {
    onFocus() {
      this.focused = true
    },
    onBlur(endpoint, payload, form) {
      this.focused = false
    }
  }
}
