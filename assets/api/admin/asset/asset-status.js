
export default {
    name: 'assetStatusApi',

    get() {
        return new Promise((resolve, reject) => {
            fetch('/api/asset_statuses.json')
                    .then(res => {
                        let json = res.json();
                        if (res.ok) {
                            resolve(json);
                        } else {
                            reject(json);
                        }
                    })

        })

    },

    persist(item) {
        return new Promise((resolve, reject) => {
            let url;
            let id = item.id;
            url = id === null ? '' : '/' + item.id;
            if (id === null) {
                delete item.id;
            }
            fetch('/api/asset_statuses' + url + '.json',
                    {'method': id === null ? 'POST' : 'PUT',
                        'body': JSON.stringify(item),
                        'headers': new Headers({'Content-Type': 'application/json; charset=utf-8'})})
                    .then(res => {
                        let json = res.json();
                        if (res.ok) {
                            resolve(json);
                        } else {
                            reject(json);
                        }
                    })
        })
    }
}