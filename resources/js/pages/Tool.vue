<template>
    <div>
        <heading class="mb-6">{{ __('Language') }}</heading>

        <card class="flex  p-3">
            <div class="flex border-b border-40 w-full" >
                <div class="w-1/4 py-6 px-8">
                    <label class="inline-block text-80 pt-2 leading-tight" for="language">{{__('Select Language')}}</label>
                </div>
                <div class="py-6 px-8 w-1/2">
                    <select v-model="currentLocal" id="language"  dusk="language"  :placeholder="__('Select Language')" class="w-full form-control form-select">
                        <option v-for="(value, key) in locals" :key="value" :value="key">
                            {{ value }}
                        </option>
                    </select>
                    <div class="help-text help-text mt-2">  </div>
                </div>
                <div class="py-6 px-2 w-1/4">
                    <button type="button" class="btn btn-default btn-primary inline-flex items-center relative ml-auto mr-3" dusk="change-language-button" @click="changeLocal">
                        <span class="">{{__('Change Language')}}</span>
                    </button>
                </div>
            </div>
        </card>
    </div>
</template>

<script>
import {default as Minimum} from 'Minimum'

export default {

    data: function () {
        return {
            initialLoading: true,
            currentLocal: window.Nova.appConfig.currentLocal,
            locals: window.Nova.appConfig.locals
        }
    },
    methods: {
        changeLocal() {
            window.location = Nova.config.base + "/nova-language-tool?lang=ar" + this.currentLocal;
        },

        async initializeComponent() {
            await this.getCurrentLocal()

            this.initialLoading = false
        },

        getCurrentLocal() {
            return Minimum(
                Nova.request().get('/nova-vendor/multilingual-nova/current-local')
            )
                .then(({ data }) => {
                    this.currentLocal = data
                    this.loading = false
                })
        },

    },
}
</script>
