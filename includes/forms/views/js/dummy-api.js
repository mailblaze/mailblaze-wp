(function() {
	if (!window.mb4wp) {
		window.mb4wp = {
			listeners: [],
			forms    : {
				on: function (event, callback) {
					window.mb4wp.listeners.push({
						event   : event,
						callback: callback
					});
				}
			}
		}
	}
})();
