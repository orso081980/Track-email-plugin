<?php 
$user_info = get_userdata(get_current_user_id());
$first_name = $user_info->first_name;
$last_name = $user_info->last_name;
$email = $user_info->user_email;
?>
<div class="container">
	<form id="contatto" name="contact-form" action="/" method="POST" data-parsley-validate="">
		<?php if(is_user_logged_in()): ?>

			<div class="row">
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="fName"><?php esc_html_e('First Name', 'first-name-netrack') ?></label>
						<input type="text" id="fName" name="fName" class="form-control" required="" value="<?= $first_name ?>" data-parsley-error-message="Please, insert your first name">
					</div>
				</div>
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="lName"><?php esc_html_e('Last Name', 'last-name-netrack'); ?></label>
						<input type="text" id="lName" name="lName" class="form-control" required="" value="<?= $last_name ?>" data-parsley-error-message="Please, insert your last name">
					</div>
				</div>
			</div>
			<?php 
		else: 
			?>
			<div class="row">
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="fName" class=""><?php esc_html_e('First Name', 'first-name-netrack') ?></label>
						<input type="text" id="fName" name="fName" class="form-control" required="" data-parsley-error-message="Please, insert your first name">

					</div>
				</div>
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="lName" class=""><?php esc_html_e('Last Name', 'last-name-netrack'); ?></label>
						<input type="text" id="lName" name="lName" class="form-control" required="" data-parsley-error-message="Please, insert your last name">
					</div>
				</div>
			</div>
			<?php 
		endif; 
		?>
		<div class="row">
			<div class="col-md-6">
				<div class="md-form mb-0">
					<label for="subject"><?php esc_html_e('Subject', 'subject-netrack'); ?></label>
					<input type="text" id="subject" name="subject" class="form-control" required="" data-parsley-error-message="Please, insert a subject">

				</div>
			</div>
			<?php if(is_user_logged_in()): ?>
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="email"><?php esc_html_e('Email', 'email-netrack'); ?></label>
						<input type="email" id="email" name="email" class="form-control" required="" data-parsley-error-message="Please, insert a valid email" value="<?= $email ?>">

					</div>
				</div>
				<?php 
			else: 
				?>
				<div class="col-md-6">
					<div class="md-form mb-0">
						<label for="email"><?php esc_html_e('Email', 'email-netrack'); ?></label>
						<input type="email" id="email" name="email" class="form-control" required="" data-parsley-error-message="Please, insert a valid email" value="<?= $email ?>">

					</div>
				</div>
				<?php
			endif;
			?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="md-form">
					<label for="message"><?php esc_html_e('Message', 'message-netrack'); ?></label>
					<textarea type="text" id="message" name="message" rows="2" class="form-control md-textarea" required="" data-parsley-error-message="Please, insert a message"></textarea>

				</div>
			</div>
		</div>
		<div class="text-center text-md-left">
			<input type="hidden" name="honeypot" id="honeypot" value="">
			<input type="hidden" name="action" value="Netrack_send">
			<input type="submit" id="submit-netrack" name="submit_form" value="Send Message">
			<div id="form-message-netrack"></div>
		</div>
	</form>
</div>