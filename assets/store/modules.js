var requireModule = require.context('../store/modules/', true, /\.js$/)
var modules = {}

requireModule.keys().forEach(fileName => {
    var moduleName = fileName.replace(/^\W+([\/\w-]+)\.js$/,'$1').replace(/\//g,'-')
    modules[moduleName] = requireModule(fileName).default 
})
export default modules