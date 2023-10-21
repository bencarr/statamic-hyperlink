import Hyperlink from './components/fieldtypes/Hyperlink.vue'
import HyperlinkItem from './components/fieldtypes/HyperlinkItem.vue'
import { SortableList } from '../../vendor/statamic/cms/resources/js/components/sortable/Sortable'

Statamic.booting(() => {
    Statamic.$components.register('hyperlink-fieldtype', Hyperlink)
    Statamic.$components.register('hyperlink-item', HyperlinkItem)
    Statamic.$components.register('sortable-list', SortableList)
})
