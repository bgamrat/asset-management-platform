// Thanks to: https://github.com/chrisvfritz/vue-enterprise-boilerplate/blob/master/src/components/_globals.js
// 
// Globally register all base components for convenience, because they
// will be used very frequently. Components are registered using the
// PascalCased version of their file name.

import Vue from 'vue'
import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

// https://webpack.js.org/guides/dependency-management/#require-context
var requireComponent = require.context(
  // Look for files in the current directory
  './Common',
  // Do not look in subdirectories
  false,
  // Only include "Common" prefixed .vue files
  /[\w-]+\.vue$/
)

// For each matching file name...
requireComponent.keys().forEach(fileName => {
  // Get the component config
  var componentConfig = requireComponent(fileName)
  // Get the PascalCase version of the component name
  var componentName = upperFirst(
    camelCase(
      fileName
        // Remove the "./_" from the beginning
        .replace(/^\.\/_/, '')
        // Remove the file extension from the end
        .replace(/\.\w+$/, '')
    )
  )

  // Globally register the component
  Vue.component(componentName.toLowerCase(), componentConfig.default || componentConfig)
})