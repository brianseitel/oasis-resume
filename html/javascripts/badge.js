(function(global) {
	
	var $ = jQuery;

	function init() {
		var badge = $('#badge');
		$.ajax({
			type: 'GET',
			url: 'http://githubbadge.appspot.com/badge/brianseitel',
			dataType: 'jsonp',
			success: function(json) {
				badge.find('.avatar').attr('src', json.user.avatar_url);
				badge.find('.recent_project').html(json.last_project);
				badge.find('.commit_sparkline').attr('src', json.commit_sparkline);
				badge.find('.repos').html(json.own_repos);
				badge.find('.forks').html(json.fork_repos);
			}
		});
	}

	$(document).ready(init);

})(window);