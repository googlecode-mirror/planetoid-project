var Feeds= {
	remove: function(id) {
		id= Feeds.ifSelection(id);
		
		if(confirm("Are you sure?\nThis action cannot be undone.") && id) {
			$.getScript('remove-feed.php?id=' + id + '&ajax=true');
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
		id= Feeds.ifSelection(id);
		var to= arguments[1] || 'a';
		
		if(id) {
			$.getScript('hide-feed.php?id=' + id + '&ajax=true&to_n=' + to);
		};
	},
	
	approve: function(id) {
		id= Feeds.ifSelection(id);
		
		if(id) {
			$.getScript('approve-feed.php?id=' + id + '&ajax=true');
		};
	},
	
	ifSelection: function(id) {
		if(id == 'sel') {
			id= [];
			
			$('table#feeds-table tbody tr.selected').each(function() {
				id.push(parseInt($(this).attr('id').split('-')[2]));
			});
		} else {
			id= [id];
		};
		
		return id.sort().join(',');
	}
};

var Plugin= {
	activate: function(dir) {
		if(dir) {
			$.getScript('activate-plugin.php?dir=' + dir + '&ajax=true');
		};
	},
	
	deactivate: function(dir) {
		if(dir) {
			$.getScript('deactivate-plugin.php?dir=' + dir + 'ajax=true');
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
	$('#loading').ajaxStart(function() { $(this).fadeIn(); });
	$('#loading').ajaxComplete(function() { $(this).fadeOut(); });
	

	$('.action-link').click(function(e) {
		e.cancelBubble= true;
		e.returnValue= false;
		e.preventDefault();
		e.stopPropagation();
	});
	
	if($('#updated').length) {
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
	
	
	if($('#error').length) {
		setTimeout(function() {$('#error').fadeOut(500);}, 21600);
	};
	
	if($('form#add-feed').length) {
		var feeds= $('table#feeds-table tbody tr input[@type=checkbox]');
		feeds.each(function() {
			$(this).click(function(e) {
				$(this).parent().parent().toggleClass('selected');
			});
		});
		
		$('#check-all').mousedown(function() {
			feeds.each(function() { $(this).attr('checked', true);$(this).parent().parent().addClass('selected'); });
		});
	};
});

function checkUpdates(el) {
	$(el).load('check-updates.php', {'ajax': true}, function() {$(this).Highlight(1000, '#ffe');});
}