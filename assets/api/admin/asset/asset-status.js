
export default {
    name: 'assetStatusApi',

    get() {
        return fetch('/api/asset_statuses.json').then((res) => res.json())
    },

    persist(item) {
        let url;
        let id = item.id;
        url = id === null ? '' : '/' + item.id;
        if (id === null) {
            delete item.id;
        }
        return fetch('/api/asset_statuses' + url + '.json',
                {'method': id === null ? 'POST' : 'PUT',
                    'body': JSON.stringify(item),
                    'headers': new Headers({'Content-Type': 'application/json; charset=utf-8'})})
                .then((res) => res.json())
                .then(json => {
                    if (typeof json.violations === "undefined") {
                        Promise.resolve(json);
                    } else {
                        throw new Error(json.detail);
                    }
                })
    }
}