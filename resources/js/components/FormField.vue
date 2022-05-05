<template>
  <DefaultField :field="field">
    <template #field>
      <div v-if="this.field.value.style==='button' || (this.field.value.style==='mix' && locals.length <= this.field.value.convert_to_list_after)">
                <span v-for="local in locals" :key="local.value" class=" mb-2 inline-flex">
                    <a :title=" (local.translated?'Translated':'Untranslated')+' Language'"
                       :class="'btn btn-lang btn-default ' + ((local.translated && translatedCount > 1) ? 'btn-with-delete ' : '') + 'btn-language-' + local.value + ' ' + (local.translated ?  'btn-translated' + (local.selected?'-selected':'') :'btn-untranslated' + (local.selected?'-selected':''))"
                       href="#" @click.prevent="localClicked(local.value)">{{local.label}}</a>
                    <a href="" v-if="local.translated && translatedCount > 1"
                       @click.prevent="deleteLocale(local.value)"
                       class="btn-delete">X</a>
                </span>
      </div>

      <div v-if="this.field.value.style==='list' || (this.field.value.style==='mix' && locals.length > this.field.value.convert_to_list_after)" class="flex relative w-full">
        <select :id="field.name" v-model="currentLocal" class="w-full form-control form-select form-select-bordered"
                :class="errorClasses" :placeholder="field.name" v-on:change="changeLocal">
          <option v-for="local in locals" :key="local.label" :value="local.value">{{ local.label }}</option>
        </select>
        <svg class="flex-shrink-0 pointer-events-none form-select-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6"><path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path></svg>
      </div>

    </template>
  </DefaultField>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova'
import {global} from '../mixin/global'

export default {
  mixins: [FormField, HandlesValidationErrors, global],

  props: ['resourceName', 'resourceId', 'field'],

  data: function () {
    return {
      currentLocal: window.Nova.appConfig.currentLocal,
      locals: window.Nova.appConfig.locals,
      fields: [],
      isEditing: false,
    }
  },

  created: function () {
    if (this.field.value.locales.length > 0) {
      this.locals = this.field.value.locales
    }
  },

  mounted() {
    if (this.field.value.style === 'list' || (this.field.value.style === 'mix' && this.field.value.locales.length > this.field.value.convert_to_list_after)) {
      let locales = this.field.value.locales;
      locales.map(function (item) {
        if (item.translated)
          item.label += " - translated";
        return item;
      });
      Object.assign(this.field, {"options": this.field.value.locales});
    }
    this.isEditing = false;
    this.$parent.$slots.default().filter(child => {
      child.children.forEach(component => {
        if (component.props.field !== undefined) {
          this.$watch('value', (value) => {
            value = (typeof value === 'string')
                ? value.replace && value.replace('<div><br></div>', '')
                : value;

            component.props.field.value = (typeof component.props.field.value === 'string')
                ? component.props.field.value.replace('<div><br></div>', '')
                : component.props.field.value;

            if (component.props.field.value !== value) {
              this.isEditing = true;
            }
          });
        }
      });
    })
  },
  computed: {
    translatedCount() {
      let count = 0;
      this.field.value.locales.forEach(locale => {
        if (locale.translated) {
          count++;
        }
      });

      return count;
    }
  }
}
</script>
