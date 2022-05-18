import Hyperlink from './components/fieldtypes/Hyperlink.vue'

Statamic.booting(() => {
    Statamic.component('hyperlink-fieldtype', Hyperlink)
})
