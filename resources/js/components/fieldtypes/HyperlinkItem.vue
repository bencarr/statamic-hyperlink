<template>
	<div class="hyperlink-item">
		<div class="hyperlink-row">
			<v-select
				v-model="type"
				append-to-body
				:options="options"
				:disabled="isReadOnly"
				:clearable="false"
				:reduce="option => option.value"
				class="hyperlink-type"
			/>
			<div class="hyperlink-destination" v-if="type !== null">
				<!-- URL text input -->
				<text-input
					v-if="type === 'url'"
					type="url"
					:is-read-only="isReadOnly"
					v-model="url"
					v-bind="meta.components.url"
				/>

				<!-- Email input -->
				<text-input
					v-if="type === 'email'"
					type="email"
					:is-read-only="isReadOnly"
					v-model="email"
					v-bind="meta.components.email"
				/>

				<!-- Phone input -->
				<text-input
					v-if="type === 'tel'"
					type="tel"
					:is-read-only="isReadOnly"
					v-model="tel"
					v-bind="meta.components.tel"
				/>

				<!-- Entry select -->
				<relationship-fieldtype
					v-if="type === 'entry'"
					ref="entries"
					handle="entry"
					v-model="selectedEntries"
					:config="meta.components.entry.config"
					:meta="entryMeta"
					:read-only="isReadOnly"
					@meta-updated="entryMeta = $event"
				/>

				<!-- Asset select -->
				<assets-fieldtype
					v-if="type === 'asset'"
					ref="assets"
					handle="asset"
					v-model="selectedAssets"
					:config="meta.components.asset.config"
					:meta="assetMeta"
					:read-only="isReadOnly"
					@meta-updated="assetMeta = $event"
				/>

				<!-- Term select -->
				<relationship-fieldtype
					v-if="type === 'term'"
					ref="terms"
					handle="term"
					v-model="selectedTerms"
					:config="meta.components.term.config"
					:meta="termMeta"
					:read-only="isReadOnly"
					@meta-updated="termMeta = $event"
				/>
			</div>
		</div>
		<div class="hyperlink-row" v-if="type !== null">
			<label :for="`${fieldId}.text`" class="sr-only" v-text="config.lang.item.text"></label>
			<text-input
				v-model="text"
				:id="`${fieldId}.text`"
				:is-read-only="isReadOnly"
				:placeholder="config.lang.item.text"
				class="hyperlink-input-text @lg:w-full"
			/>
			<toggle-fieldtype
				v-model="newWindow"
				v-if="!['email', 'tel'].includes(type)"
				handle="target"
				:read-only="isReadOnly"
				:config="{ inline_label: config.lang.item.new_window }"
				class="hyperlink-input-target"
			/>
		</div>
	</div>

</template>
<script>
export default {
	props: ['value', 'meta', 'config', 'fieldId', 'isReadOnly'],
	data() {
		// If you collapse a Bard field without saving, the current values are stored in `value`,
		// but won’t match `meta` since they were never saved. When the component re-mounts,
		// we need to populate them from `value` rather than meta
		const type = this.value ? this.value.type : this.getInitialType(this.meta)
		const link = this.value ? this.value.link : this.meta.link
		const text = this.value ? this.value.text : this.meta.text
		const newWindow = this.value ? this.value.newWindow : this.meta.newWindow

		return {
			// Core data
			type,
			text,
			newWindow,

			// Config
			options: this.config.options,

			// Models
			url: type === 'url' ? link : null,
			email: this.parseValue(link, 'mailto:'),
			tel: this.parseValue(link, 'tel:'),
			selectedEntries: [this.parseValue(link, 'entry::')].filter(v => v),
			entryMeta: this.value?.components?.entry?.meta || this.meta.entry.meta,
			selectedAssets: [this.parseValue(link, 'asset::')].filter(v => v),
			assetMeta: this.value?.components?.asset?.meta || this.meta.asset.meta,
			selectedTerms: [this.parseValue(link, 'term::')].filter(v => v),
			termMeta: this.value?.components?.term?.meta || this.meta.term.meta,
		}
	},

	computed: {
		typeLabel() {
			return this.options.find(o => o.value === this.type).label || '?'
		},
		replicatorPreview() {
			if (!this.returnValue) {
				return null
			}

			return [this.typeLabel, this.text, this.augmentedLink].filter(v => v).join(' / ')
		},
		augmentedLink() {
			if (this.type === 'url') {
				return this.url ? this.url : null
			}

			if (this.type === 'email') {
				return this.email ? `mailto:${this.email}` : null
			}

			if (this.type === 'tel') {
				return this.tel ? `tel:${this.tel}` : null
			}

			if (this.type === 'entry') {
				return this.selectedEntries.length ? `entry::${this.selectedEntries[0]}` : null
			}

			if (this.type === 'asset') {
				return this.selectedAssets.length ? `asset::${this.selectedAssets[0]}` : null
			}

			if (this.type === 'term') {
				return this.selectedTerms.length ? `term::${this.selectedTerms[0]}` : null
			}

			return null
		},
		returnValue() {
			if (this.type === null) {
				return null
			}

			return {
				type: this.type,
				link: this.augmentedLink,
				text: this.text,
				newWindow: ['email', 'tel'].includes(this.type) ? false : this.newWindow,
			}
		},
	},
	methods: {
		getInitialType(meta) {
			return meta.type || this.config.options[0].value
		},
		parseValue(value, prefix, fallback = null) {
			if (!value) {
				return fallback
			}
			return value.startsWith(prefix) ? value.substring(prefix.length) : fallback
		},
	},
	watch: {
		type(type) {
			if (this.metaChanging) {
				return
			}

			if (type === 'entry' && !this.selectedEntries.length) {
				setTimeout(() => this.$refs.entries.linkExistingItem(), 0)
			}

			if (type === 'asset' && !this.selectedAssets.length) {
				setTimeout(() => this.$refs.assets.openSelector(), 0)
			}

			if (type === 'term' && !this.selectedTerms.length) {
				setTimeout(() => this.$refs.terms.linkExistingItem(), 0)
			}
		},
		meta(meta) {
			// Flag that we're changing sites
			this.metaChanging = true

			// Update the component data with the new meta state
			this.type = this.getInitialType(meta)
			this.text = meta.text
			this.newWindow = meta.newWindow

			this.url = meta.type === 'url' ? meta.link : null
			this.email = this.parseValue(meta.link, 'mailto:')
			this.tel = this.parseValue(meta.link, 'tel:')
			this.selectedEntries = [this.parseValue(meta.link, 'entry::', [])].filter(v => v)
			this.selectedAssets = [this.parseValue(meta.link, 'asset::', [])].filter(v => v)
			this.selectedTerms = [this.parseValue(meta.link, 'term::', [])].filter(v => v)

			// Listen for changes again
			this.$nextTick(() => this.metaChanging = false)
		},
		returnValue(value) {
			// Don’t fire an update when changing sites so unsaved changes handler
			// doesn't think the field was edited
			if (this.metaChanging) return

			this.$emit('input', value)
		},
	},
}
</script>
<style>
/* Move selected asset border to wrapper div to support border radius */
.hyperlink-fieldtype .asset-table-listing {
	border-radius: 3px;
	border-width: 1px;
}

.hyperlink-fieldtype .asset-table-listing table {
	border-width: 0;
}

.hyperlink-fieldtype .asset-table-listing tbody tr {
	border-bottom-width: 0;
}

/* Tighten spacing to match link type dropdown height */
.hyperlink-fieldtype .asset-table-listing tbody tr td {
	padding: 4px;
}
</style>
