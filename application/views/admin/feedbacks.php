<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token form-control">

<div class="wrapper">
	<div class="bg-light-custom p-3">
		<?php if ($feedbacks->num_rows() > 0) : ?>
			<div class="text-left">
				<a href="<?php echo base_url('clear-feedbacks'); ?>" class="btn btn-danger clear_feedbacks">
					<i class="fas fa-trash-alt mr-2"></i>Clear Data
				</a>
			</div>
		<?php endif; ?>

		<table id="feedbackstable" data-toggle="table" data-search="true" data-show-export="true" data-buttons-prefix="btn-md btn" data-buttons-align="right" data-pagination="true">
			<thead class="text-light" style="background:#294a63">
				<tr>
					<th data-field="name" data-sortable="true">Name</th>
					<th data-field="mail" data-sortable="true">E-mail</th>
					<th data-field="msg" data-sortable="true">Message</th>
					<th data-field="date" data-sortable="true">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($feedbacks->result_array() as $msg) : ?>
					<tr>
						<td><?php echo $msg['name'] ?></td>
						<td><a href="mailto:<?php echo $msg['user_mail'] ?>"><?php echo $msg['user_mail'] ?></a></td>
						<td><?php echo $msg['bdy'] ?></td>
						<td class="date"><?php echo $msg['date'] ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>



<script>
	$(document).ready(function() {
		$(document).on('click', '.clear_feedbacks', function(e) {
			e.preventDefault();

			var con = confirm("Are you sure you want to clear this data?");
			if (con === false) {
				return false;
			} else {
				var linkurl = $(this).attr('href');
				window.location.assign(linkurl);
			}
		});
	});
</script>