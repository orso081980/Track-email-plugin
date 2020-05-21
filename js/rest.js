
$( document ).ready(function() {
	console.log(wpApiSettings.page_num);
	const table = document.querySelector('#table');
	let lastitem;
	let suggest;
	let clickRest = $('#pagination li');
	

	$.ajax({

		method: 'GET',
		url: wpApiSettings.root+`my-letter/my-letter-get`,
		contentType: 'application/json; charset=utf-8',
		beforeSend: function ( xhr ) {
			xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
		},
		dataType: 'json',
		success: ajaxResponse

	});

	function ajaxResponse(data) {
		console.log(data);
		let str = [];
		suggest = data;
		str += `<h2>Feedbacks</h2>
		<table class="table">
		<thead>
		<tr>
		<th>ID</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Subject</th>
		<th>Email</th>
		<th>Message</th>
		</tr>
		</thead>
		<tbody>`;

		for ( let i = 0; i < suggest.length; i ++ ) {

			str += `<tr data-id="${suggest[i].id}">`;
			str += `<td>${suggest[i].id}</td>`;
			str += `<td>${suggest[i].fname}</td>`;
			str += `<td>${suggest[i].lname}</td>`;
			str += `<td>${suggest[i].email}</td>`;
			str += `<td>${suggest[i].subject}</td>`;
			str += `<td>${suggest[i].message}</td>`;
			str += '</tr>';
		}

		str += `</tbody>
		</table>`;
		table.innerHTML = str;

	}

	clickRest.click(function(e) {

		let clickId = $(this).attr('id');
		e.preventDefault();
		$.ajax({

			method: 'GET',
			url: wpApiSettings.root+`my-letter/my-letter-get?page=${clickId}`,
			contentType: 'application/json; charset=utf-8',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
			},
			dataType: 'json',
			success: ajaxResponse

		});

	})

});
