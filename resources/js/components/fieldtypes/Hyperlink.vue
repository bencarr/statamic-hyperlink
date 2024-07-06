<template>
	<div>
		<template v-if="meta.profile.max_items > 1">
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
					<div
						class="hyperlink-sortable-item"
						v-for="(item, i) in links"
						:key="`${renderId}.${i}`"
						:class="{'hyperlink-sortable-item--sortable': showRowControls }"
					>
						<div
							class="absolute top-0 left-0 w-6 h-full flex flex-col justify-between flex-none"
							:class="{ 'hyperlink-sortable-item-controls--hide': !showRowControls }"
						>
							<div class="hyperlink-sortable-item-handle flex items-center justify-center px-1 py-2">
								<svg-icon name="light/drag-dots" class="h-4 w-3" />
							</div>
							<button
								class="flex items-center justify-center group w-full px-1 pt-1 pb-2"
								@click="removeLink(i)"
								:title="meta.lang.remove"
								:aria-label="meta.lang.remove"
							>
								<svg-icon
									name="micro/trash"
									class="w-3 h-3 group-hover:text-red-500 transition duration-150"
								/>
							</button>
						</div>
						<div class="grow hyperlink-item-wrapper" :class="{ 'hyperlink-item-wrapper--with-controls': showRowControls }">
							<hyperlink-item
								v-model="links[i]"
								:field-id="`${fieldId}.i`"
								:is-read-only="isReadOnly"
								:value="item"
								:meta="metaDefaults"
								:config="meta"
							/>
						</div>
					</div>
				</div>
			</sortable-list>
			<div class="mt-4">
				<button
					v-if="canAddMoreLinks"
					class="text-button text-sm text-blue dark:text-dark-blue-100 hover:text-gray-800 dark:hover:text-dark-100 mr-6 flex items-center outline-none"
					@click="addLink()"
				>
					<svg-icon name="micro/plus" class="w-2 h-2 mr-1" />
					<span v-text="meta.lang.add"></span>
				</button>
			</div>
		</template>
		<template v-else>
			<div v-for="(item, i) in links" :key="`${renderId}.${i}`">
				<hyperlink-item
					v-model="links[i]"
					:field-id="`${fieldId}.i`"
					:is-read-only="isReadOnly"
					:value="item"
					:meta="metaDefaults"
					:config="meta"
				/>
			</div>
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
			links: this.normalizeItems(this.meta.items),
			metaDefaults: Object.assign({}, this.meta.defaults),
			renderId: this.generateId(),
		}
	},
	computed: {
		returnValue() {
			if (this.links.length === 1 && (this.links[0] === null || this.links[0].type === null)) {
				return null
			}

			const allowedKeys = ['type', 'link', 'text', 'newWindow']
			return this.links.map((link) => Object.fromEntries(allowedKeys.map(key => [key, link[key]])))
		},
		showRowControls() {
			return this.meta.profile.max_items > 1 && this.links.length > 1
		},
		canAddMoreLinks() {
			return this.links.length < this.meta.profile.max_items
		},
		replicatorPreview() {
			if (!this.returnValue) {
				return null
			}

			return this.links.map(link => `${link.text} (${link.link})`).join(' / ')
		},
	},
	methods: {
		sorted(value) {
			this.links = value
			this.renderId = this.generateId()
		},
		addLink() {
			if (!this.canAddMoreLinks) return

			this.links.push({ ...this.meta.defaults })
		},
		removeLink(i) {
			if ((this.links[i].link === null && !this.links[i].text) || confirm(this.meta.lang.confirm_removal)) {
				this.links.splice(i, 1)
				this.renderId = this.generateId()
			}
		},
		generateId() {
			return Math.random().toString(16).substring(2, 10);
		},
		normalizeItems(items) {
			// Normalize links, which might be:
			// - Object (single-items)
			// - Array of objects (multiple items)
			// - null (unsaved)
			const links = Array.from(items).filter(Boolean)
			if (!links.length) links.push(this.meta.defaults)

			return links.slice()
		},
	},
	watch: {
		returnValue() {
			// Don’t fire an update when changing sites so unsaved changes handler
			// doesn't think the field was edited
			if (this.metaChanging) return

			this.updateDebounced(this.returnValue)
		},
		meta(meta, oldMeta) {
			if (JSON.stringify(meta) === JSON.stringify(oldMeta)) return;

			this.metaChanging = true

			this.links = this.normalizeItems(meta.items)
			this.metaDefaults = meta.defaults
			this.renderId = this.generateId()

			this.metaChanging = false
		}
	},
	mounted() {
		// Pretend the Hyperlink field is within a Grid so nested Asset fields
		// hide the "Browse" toggle after making a selection
		this.grid = true
	},
}
</script>

