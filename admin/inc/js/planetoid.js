var Feeds= {
	remove: function(id) {
		if(confirm("Are you sure?\nThis action cannot be undone.")) {
			$.get('remove-feed.php', {'id': id, 'ajax': true}, function(resp) { eval(resp); });
		};
	},
	
	add: function(opts) {
		if(opts.url && opts.email) {
			$.get('add-feed.php', {'url': opts.url, 'avatar': opts.avatar, 'email': opts.email}, function(resp) { eval(resp); });
		} else {
			alert("Feed URL and submitters email are required.");
		};
	},
	
	hide: function(id) {
		if(id) {
			$.get('hide-feed.php', {'id': id, 'ajax': true}, function(resp) { eval(resp); });
		};
	},
	
	approve: function(id) {
		if(id) {
			$.get('approve-feed.php', {'id': id, 'ajax': true}, function(resp) { eval(resp); });
		};
	}
};

var Plugin= {
	activate: function(dir) {
		if(dir) {
			$.get('activate-plugin.php', {'dir': dir, 'ajax': true}, function(resp) { eval(resp); });
		};
	},
	
	deactivate: function(dir) {
		if(dir) {
			$.get('deactivate-plugin.php', {'dir': dir, 'ajax': true}, function(resp) { eval(resp); });
		}
	}
};

var Settings= {
	set: function(name, value) {
		$.post('setting-set.php', {'name': name, 'value': value, 'ajax': true}, function(resp) { eval(resp); });
	},
	
	setTheme: function(theme) {
		$('#themes').load('setting-set.php', {'theme_dir_name': theme, 'ajax': true});
	}
};

$(document).ready(function() {
	$('#loading').ajaxStart(function() { $(this).css('display', ''); });
	$('#loading').ajaxComplete(function() { $(this).css('display', 'none'); });
	

	$('.action-link').click(function(e) {
		e.cancelBubble= true;
		e.returnValue= false;
		e.preventDefault();
		e.stopPropagation();
	});
	
	if($('#updated')) {
		$('#updated').Highlight(2000, '#ffa');
		
		setTimeout(function() {
			if($('#updated').css('display') != 'none') {
				$('#updated').fadeOut(500);
			};
		}, 21600);
		
		$('#updated').click(function() {
			$(this).fadeOut(500);
		});
	};
	
	
	if($('#error').length != 0) {
		setTimeout(function() {$('#error').fadeOut(500);}, 21600);
	};
});

function checkUpdates(el) {
	$(el).load('check-updates.php', {'ajax': true}, function() {$(this).Highlight(1000, '#ffe');});
}