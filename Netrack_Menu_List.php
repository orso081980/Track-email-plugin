<div class="wrap">
	<h2>Feedbacks</h2>
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
		<tbody>
			<?php foreach ($rows as $values): ?>
				<tr>
					<?php foreach ($values as $value): ?>
						<td><?= $value; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>