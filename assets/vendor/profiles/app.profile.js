var profile = (function () {
    return {
        basePath: "../../vendor/dojo",
        releaseDir: "../../../public/build",
        releaseName: "lib",
        action: "release",
        layerOptimize: "closure",
        optimize: "closure",
        cssOptimize: "comments",
        mini: true,
        stripConsole: "warn",
        selectorEngine: "lite",
        defaultConfig: {
            hasCache: {
                "dojo-built": 1,
                "dojo-loader": 1,
                "dom": 1,
                "host-browser": 1,
                "config-selectorEngine": "lite"
            },
            async: 1
        },
        optimizeOptions: {
            languageIn: 'ECMASCRIPT6',
            languageOut: 'ECMASCRIPT5'
        },
        // Providing hints to the build system allows code to be conditionally removed on a more granular level than simple
        // module dependencies can allow. This is especially useful for creating tiny mobile builds. Keep in mind that dead
        // code removal only happens in minifiers that support it! Currently, only Closure Compiler to the Dojo build system
        // with dead code removal. A documented list of has-flags in use within the toolkit can be found at
        // <http://dojotoolkit.org/reference-guide/dojo/has.html>.
        // these are all the has feature that affect the loader and/or the bootstrap
        // the settings below are optimized for the smallest AMD loader that is configurable
        // and include dom-ready support
        staticHasFeatures: {
            'config-dojo-loader-catches': 0,
            'config-tlmSiblingOfDojo': 0,
            'dojo-amd-factory-scan': 0,
            'dojo-combo-api': 0,
            'dojo-config-api': 1,
            'dojo-config-require': 0,
            'dojo-debug-messages': 0,
            'dojo-dom-ready-api': 1,
            'dojo-firebug': 0,
            'dojo-guarantee-console': 1,
            // https://dojotoolkit.org/documentation/tutorials/1.10/device_optimized_builds/index.html
            // https://dojotoolkit.org/reference-guide/1.10/dojo/has.html
            'dom-addeventlistener': 1,
            'dom-qsa': 1,
            'dom-qsa2.1': 1,
            'dom-qsa3': 1,
            'dom-matches-selector': 1,
            'json-stringify': 1,
            'json-parse': 1,
            'bug-for-in-skips-shadowed': 0,
            'native-xhr': 1,
            'native-xhr2': 1,
            'native-formdata': 1,
            'native-response-type': 1,
            'native-xhr2-blob': 1,
            'dom-parser': 1,
            'activex': 0,
            'script-readystatechange': 1,
            'ie-event-behavior': 0,
            'MSPointer': 0,
            'touch-action': 1,
            'dom-quirks': 0,
            'array-extensible': 1,
            'console-as-object': 1,
            'jscript': 0,
            'event-focusin': 1,
            'events-mouseenter': 1,
            'events-mousewheel': 1,
            'event-orientationchange': 1,
            'event-stopimmediatepropagation': 1,
            'touch-can-modify-event-delegate': 0,
            'dom-textContent': 1,
            'dom-attributes-explicit': 1,
            // unsupported browsers
            'air': 0,
            'wp': 0,
            'khtml': 0,
            'wii': 0,
            'quirks': 0,
            'bb': 0,
            'msapp': 0,
            'opr': 0,
            'android': 0,
            'svg': 1,
            // Deferred Instrumentation is disabled by default in the built version
            // of the API but we still want to enable users to activate it.
            // Set to -1 so the flag is not removed from the built version.
            'config-deferredInstrumentation': -1,
            // Dojo loader will have 'has' api, but other loaders such as
            // RequireJS do not. So, let's not mark it static.
            // This will allow RequireJS loader to fetch our modules.
            'dojo-has-api': -1,
            'dojo-inject-api': 1,
            'dojo-loader': 1,
            'dojo-log-api': 0,
            'dojo-modulePaths': 0,
            'dojo-moduleUrl': 0,
            'dojo-publish-privates': 0,
            'dojo-requirejs-api': 0,
            'dojo-sniff': 0,
            'dojo-sync-loader': 0,
            'dojo-test-sniff': 0,
            'dojo-timeout-api': 0,
            'dojo-trace-api': 0,
            //'dojo-undef-api': 0,
            'dojo-v1x-i18n-Api': 1, // we still need i18n.getLocalization
            'dojo-xhr-factory': 0,
            'dom': -1,
            'host-browser': -1,
            'extend-dojo': 1
        },
        packages: [
            {name: 'app', location: '../../app'},
            'dojo',
            'dijit',
            'dojox',
            'put-selector',
            'xstyle',
            'rql',
            'dstore',
            'dgrid'
        ],
        layers: {
            "dojo/dojo": {
                include: ["dojo/dojo", "dojo/i18n", "dojo/domReady"],
                customBase: true,
                boot: true
            },
            "app/lib/common": {
                include: ["app/lib/common"]
            },
            "app/admin/menu": {
                include: ["app/admin/menu"]
            },
            "app/test": {
                include: ["app/test"]
            },
            "app/admin/asset/asset_status": {
                include: ["app/admin/asset/asset_status"]
            },
            "app/admin/asset/barcodes": {
                include: ["app/admin/asset/barcodes"]
            },
            "app/admin/asset/brand": {
                include: ["app/admin/asset/brand"]
            },
            "app/admin/asset/carrier": {
                include: ["app/admin/asset/carrier"]
            },
            "app/admin/asset/categories": {
                include: ["app/admin/asset/categories"]
            },
            "app/admin/asset/category": {
                include: ["app/admin/asset/category"]
            },
            "app/admin/asset/common": {
                include: ["app/admin/asset/common"]
            },
            "app/admin/asset/custom_attributes": {
                include: ["app/admin/asset/custom_attributes"]
            },
            "app/admin/asset/equipment": {
                include: ["app/admin/asset/equipment"]
            },
            "app/admin/asset/issue": {
                include: ["app/admin/asset/issue"]
            },
            "app/admin/asset/issue_status": {
                include: ["app/admin/asset/issue_status"]
            },
            "app/admin/asset/issue_type": {
                include: ["app/admin/asset/issue_type"]
            },
            "app/admin/asset/issue_workflow": {
                include: ["app/admin/asset/issue_workflow"]
            },
            "app/admin/asset/location": {
                include: ["app/admin/asset/location"]
            },
            "app/admin/asset/location_type": {
                include: ["app/admin/asset/location_type"]
            },
            "app/admin/asset/manufacturer": {
                include: ["app/admin/asset/manufacturer"]
            },
            "app/admin/asset/trailer": {
                include: ["app/admin/asset/trailer"]
            },
            "app/admin/asset/trailers": {
                include: ["app/admin/asset/trailers"]
            },
            "app/admin/asset/transfer_status": {
                include: ["app/admin/asset/transfer_status"]
            },
            "app/admin/asset/transfer": {
                include: ["app/admin/asset/transfer"]
            },
            "app/admin/asset/vendor": {
                include: ["app/admin/asset/vendor"]
            },
            "app/admin/client/client": {
                include: ["app/admin/client/client"]
            },
            "app/admin/client/contract": {
                include: ["app/admin/client/contract"]
            },
            "app/admin/common/people": {
                include: ["app/admin/common/people"]
            },
            "app/admin/menu": {
                include: ["app/admin/menu"]
            },
            "app/admin/schedule/event": {
                include: ["app/admin/schedule/event"]
            },
            "app/admin/schedule/time_span_type": {
                include: ["app/admin/schedule/time_span_type"]
            },
            "app/admin/venue/venue": {
                include: ["app/admin/venue/venue"]
            },
            "app/lib/common": {
                include: ["app/lib/common"]
            },
            "app/lib/grid": {
                include: ["app/lib/grid"]
            }
        },
        resourceTags: {
            amd: function (filename, mid) {
                return /\.js$/.test(filename);
            }
        }
    };
})();
