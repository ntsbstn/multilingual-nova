<template>
    <div class="p-3">
        <select v-model="currentLocal"  class="w-full form-control form-select"
                v-on:change="changeLocal">
            <option v-for="(value, key) in locals" :key="value" :value="key">
                {{ value }}
            </option>
        </select>
    </div>
</template>

<script>
export default {

    data: function () {
        return {
            currentLocal: window.Nova.appConfig.currentLocal,
            locals: window.Nova.appConfig.locals
        }
    },
    methods: {
        changeLocal() {
            window.location = this.replaceUrlParam(window.location.href, 'lang', this.currentLocal);
        },

        async initializeComponent() {
            await this.getCurrentLocal()
        },

        getLocals() {
            return Nova.request().get('/nova-vendor/multilingual-nova/locals')
                .then(({ data }) => {
                    this.locals = data
                })
        },

        getCurrentLocal() {
            return Nova.request().get('/nova-vendor/multilingual-nova/current-local')
                .then(({ data }) => {
                    this.currentLocal = data
                    this.loading = false
                })
        },


        replaceUrlParam(url, paramName, paramValue)
        {
            if (paramValue == null) {
                paramValue = '';
            }
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
            if (url.search(pattern)>=0) {
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            url = url.replace(/[?#]$/,'');
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        }

    }
}
</script>
