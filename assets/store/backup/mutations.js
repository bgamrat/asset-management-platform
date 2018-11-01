
export var increment = state => {
  state.count++
  state.history.push('increment')
}

export var decrement = state => {
  state.count--
  state.history.push('decrement')
}