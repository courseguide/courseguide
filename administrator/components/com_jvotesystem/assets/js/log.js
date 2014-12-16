/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
* @projectsite www.joomess.de/projects/jvotesystem
* @authors Johannes Meßmer, Andreas Fischer
* @copyright (C) 2010 - 2012 Johannes Meßmer
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

jQuery(document).ready(function() {
	if(jVS != undefined && jQuery('table.logs').length > 0) {
		jVS.isExecutionTimeSet(function(result) {
			if(result) {
				jVS.conf.lastLogID = 0;
				checkForNewLogs();
			}
		});
	}
});

function checkForNewLogs() {
	jVS.req({"task": "checkForNewLogs", "lastID": jVS.conf.lastLogID, "type": jQuery('table.logs').dp("type")}, function(resp) {
		if(resp.success) {
			jVS.conf.lastLogID = resp.last;
			
			if(resp.newEntries) {
				for(i = 0; i < resp.logs.length; i++) {
					var el = jQuery(resp.logs[i]);//.css("display", "table").hide();
					jQuery('table.logs').prepend(el);
					el.find('td')
					 .wrapInner('<div style="display: none;" />')
					 .parent()
					 .find('td > div')
					 .slideDown(700);
				}
			}
			if(parseInt(jQuery('table.logs').dp('max-logs')) < jQuery('table.logs tbody').children().length) {
				jQuery( jQuery('table.logs tbody').children()[4] ).nextAll().remove();
			}
			
			//Next..
			checkForNewLogs();
		} else console.log("Error while updating logs: " + resp.error);
	}, function() {
		window.setTimeout(function() {
			checkForNewLogs();
		}, 5000);
		console.log("Failed to update logs!");
	}, true);
}