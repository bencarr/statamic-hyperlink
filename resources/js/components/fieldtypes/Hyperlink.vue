<template>
	<div>
		<template v-if="config.max_items > 1">
			<sortable-list
				:value="links"
				:vertical="true"
				item-class="hyperlink-sortable-item"
				handle-class="hyperlink-sortable-item-handle"
				append-to="body"
				constrain-dimensions
				@input="sorted($event)"
				@dragstart="$emit('focus')"
				@dragend="$emit('blur')"
			>
				<div class="hyperlink-sortable-list">
					<div class="hyperlink-sortable-item" v-for="(item, i) in links">
						<div class="hyperlink-sortable-item-handle h-8 w-3 flex items-center justify-center" v-if="showRowControls">
							<svg-icon name="light/drag-dots" class="h-4 w-3" />
						</div>
						<div class="grow">
							<hyperlink-item v-model="links[i]" :field-id="`${fieldId}.i`" :is-read-only="isReadOnly" :value="item" :meta="meta.items[i] || meta.defaults"/>
						</div>
						<button v-if="showRowControls" class="btn btn-sm btn-delete" @click="links.splice(i, 1)" aria-label="Remove Link">
							<svg-icon name="micro/trash" class="w-3 h-3 text-gray-700 group-hover:text-black transition duration-150" />
						</button>
					</div>
				</div>
			</sortable-list>
			<div class="mt-4">
				<button v-if="canAddMoreLinks" class="btn-round flex items-center justify-center h-5 w-5" @click="addLink()" aria-label="Add">
					<svg-icon name="micro/plus" class="w-2 h-2 text-gray-700 group-hover:text-black transition duration-150" />
				</button>
			</div>
		</template>
		<template v-else>
			<hyperlink-item v-model="links[0]" :field-id="`${fieldId}.0`" :is-read-only="isReadOnly" :value="links[0]" :meta="meta.items[0]" :defaults="meta.defaults" />
		</template>
		<!--<div class="mt-4 font-mono bg-gray-200 border p-2 text-2xs rounded" style="white-space: pre">{{ JSON.stringify(returnValue, null, 2) }}</div>-->
	</div>
</template>
<script>
export default {
	mixins: [Fieldtype],
	data() {
		return {
			// Flag for multi-site changes
			metaChanging: false,

			// Links
			links: this.value ?? [null],
		}
	},
	computed: {
		returnValue() {
			if (this.links.length === 1 && (this.links[0] === null || this.links[0].type === null)) {
				return null
			}

			return this.links
		},
		showRowControls() {
			return this.config.max_items > 1 && this.links.length > 1
		},
		canAddMoreLinks() {
			return this.links.length < this.config.max_items
		},
	},
	methods: {
		sorted(value) {
			this.links = value
		},
		addLink() {
			if (!this.canAddMoreLinks) return

			this.links.push(null)
		},
	},
	watch: {
		returnValue() {
			// Don’t fire an update when changing sites so unsaved changes handler
			// doesn't think the field was edited
			if (this.metaChanging) return

			this.updateDebounced(this.returnValue)
		},
	},
	mounted() {
		// Pretend the Hyperlink field is within a Grid so nested Asset fields
		// hide the "Browse" toggle after making a selection
		this.grid = true;
	},
}
</script>

