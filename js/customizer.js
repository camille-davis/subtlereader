(function ($) {
	wp.customize.bind('ready', function () {
		const sectionsWithReset = [
			'subtle_typography_fonts',
			'subtle_typography_font_sizes',
			'subtle_layout',
		];
		sectionsWithReset.forEach((sectionId) => {
			wp.customize.section(sectionId).controls().forEach((control) => {
			// Create and insert 'Reset' button.
			const $resetButton = $(
				'<button type="button" class="button reset-button">' +
					subtleCustomizer.resetText + // eslint-disable-line no-undef
					'</button>'
			);
			control.container.find('input').after($resetButton);

			// On reset, repopulate input with default value.
			$resetButton.on('click', () => {
				const defaultValue = control.setting.default;
				control.container
					.find('input')
					.val(defaultValue)
					.trigger('change');
			});
			});
		});
	});
})(jQuery);
