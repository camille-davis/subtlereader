/**
 * Add editor and footer container classes to apply style.css styles.
 */
(function () {
	function addFooterClasses() {
		const widgetEditor = document.getElementById('widgets-editor');
    if (!widgetEditor) {
      return;
    }

		const observer = new MutationObserver(function () {
      const widgetAreas = document.querySelectorAll('[data-widget-area-id]');
      if (widgetAreas.length === 0) {
        return;
      }

		  widgetAreas.forEach(function (area) {
			  const id = area.dataset.widgetAreaId;
			  const layout = area.querySelector('.block-editor-block-list__layout');
			  if (!layout) {
				  return;
			  }

			  if (id === 'footer') {
				  layout.classList.add('footer');
			  }
    });

      observer.disconnect();
		});
		observer.observe(widgetEditor, { childList: true, subtree: true });
	}

	function addEditorClasses() {
    const blockEditor = document.getElementById('editor');
    if (!blockEditor) {
      return;
    }

		const observer = new MutationObserver(function () {
      const iframe = document.querySelector('iframe');
      if (!iframe) {
        return;
      }

      const rootContainer = iframe.contentDocument.querySelector('.is-root-container');
      if (!rootContainer) {
        return;
      }

      rootContainer.classList.add('entry-content');

      observer.disconnect();
		});
		observer.observe(blockEditor, { childList: true, subtree: true });
	}

	document.addEventListener('DOMContentLoaded', function () {
		addEditorClasses();
		addFooterClasses();
	});
})();

