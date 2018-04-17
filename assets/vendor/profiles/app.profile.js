var profile = (function () {
    return {
        basePath: "../../vendor/dojo",
        releaseDir: "../../public/build",
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
        staticHasFeatures: {
            "config-deferredInstrumentation": 0,
            "config-dojo-loader-catches": 0,
            "config-tlmSiblingOfDojo": 0,
            "dojo-amd-factory-scan": 0,
            "dojo-combo-api": 0,
            "dojo-config-api": 1,
            "dojo-config-require": 0,
            "dojo-debug-messages": 1,
            "dojo-dom-ready-api": 1,
            "dojo-firebug": 0,
            "dojo-guarantee-console": 1,
            "dojo-has-api": 1,
            "dojo-inject-api": 1,
            "dojo-loader": 1,
            "dojo-log-api": 0,
            "dojo-modulePaths": 0,
            "dojo-moduleUrl": 0,
            "dojo-publish-privates": 0,
            "dojo-requirejs-api": 0,
            "dojo-sniff": 1,
            "dojo-sync-loader": 0,
            "dojo-test-sniff": 0,
            "dojo-timeout-api": 0,
            "dojo-trace-api": 0,
            "dojo-undef-api": 0,
            "dojo-v1x-i18n-Api": 1,
            "dom": 1,
            "host-browser": 1,
            "extend-dojo": 1
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
