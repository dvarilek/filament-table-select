function l({ state: i, isMultiple: s }) {
  return {
    state: i,
    isMultiple: s,
    isOptionSelected: function (t = null) {
      return !this.isMultiple && t === null
        ? this.state !== null
        : Array.isArray(this.state)
          ? this.state.includes(t)
          : !1
    },
    hasSelectedOptions: function () {
      return this.isMultiple
        ? Array.isArray(this.state) && this.state.length > 0
        : this.state !== null
    },
    deselectOption: function (t = null) {
      if (!this.isMultiple && t === null) {
        this.state = null
        return
      }
      Array.isArray(this.state) &&
        (this.state = this.state.filter((e) => e !== t))
    },
  }
}
export { l as default }
