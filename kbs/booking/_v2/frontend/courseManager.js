
var cmProperties = {
	//ajaxServicesUrl: "http://test.aerialsilks.at/kbs/booking/_v2/ajax/public-ajax-services.php?cmd=",
	ajaxServicesUrl: "/kbs/course_ajax_services.php?cmd=",
	
};

var cm = {
	Courses: {
		getSearchResult: function () {
			var time = new Date();
			time = time.getTime();
			alert(cmProperties.ajaxServicesUrl + 'Courses.GetSearchResult&t=' + time);
			jQuery.post(cmProperties.ajaxServicesUrl + 'Courses.GetSearchResult&t=' + time, {}, function(data) {
				var d = JSON.parse(data);
				if(d.error != 1) {
					alert('success');
					// var h = [];
					// h.push('<table>');
					// h.push('	<tbody>');
					// h.push('		<tr>');
					// h.push('			<th>Sortierung</th>');
					// h.push('			<th style="min-width: 250px;">Name</th>');
					// h.push('			<th>Status</th>');
					// h.push('		</tr>');
					// h.push('	</tbody>');
					// h.push('</table>');
					// $('.rb-table').empty();
					// $('.rb-table').html(h.join(""));
					// var da = d.data;
					// var length = da.length;
					// if(length == 0) {
						// $('.rb-table').append('<br/>Keine Einträge gefunden.');
						// } else {
							// var curr;
							// var statusHtml;
							// for(var i = 0; i < length; i++) {
								// curr = da[i];
								// if(curr.status == 1) {
									// statusHtml = '<div class="status-active">✔</div>';
								// }else {
									// statusHtml = '<div class="status-inactive">✖</div>';
								// }
								// h = [];
								// h.push('<tr id="' + curr.id + '">');
								// h.push('	<td class="td-center">' + curr.sort_no + '</td>');
								// h.push('	<td>' + curr.name + '</td>');
								// h.push('	<td class="td-center">' + statusHtml + '</td>');
								// h.push('</tr>');
// 								
								// $('.rb-table table tbody').append(h.join(""));
							// }
						// }
					// cm.SelectableManager.init('.rb-table table');
					// cm.CourseFormats.initDblClick();
// 					
					// if(isNavigationRequired) {
						// $('.container-detail').hide();
						// $('.container-overview').show();
					// }
				}else {
					alert(d.errtxt);
				}
			});
		},
		init: function() {
			cm.Courses.getSearchResult();
		}
	}
};
