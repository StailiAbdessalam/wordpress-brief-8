const haystack = ['core/group'];

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'lh/fseFixes/layoutSettings',
	(settings, name) => {
		if (!haystack.includes(name)) {
			return settings;
		}

		const newSettings = {
			...settings,
			supports: {
				...(settings.supports || {}),
				layout: {
					...(settings.supports.layout || {}),
					allowEditing: false,
					allowSwitching: false,
					allowInheriting: true,
				},
				__experimentalLayout: {
					...(settings.supports.__experimentalLayout || {}),
					allowEditing: false,
					allowSwitching: false,
					allowInheriting: true,
				},
			},
		};
		return newSettings;
	},
	20
);

wp.hooks.addFilter(
	'blocks.getBlockAttributes',
	'lh/fseFixes',
	(attributes, blockType) => {
		if (!haystack.includes(blockType.name)) {
			return attributes;
		}

		attributes = {
			...attributes,
			layout: {
				inherit: true,
			},
		};

		return attributes;
	}
);
