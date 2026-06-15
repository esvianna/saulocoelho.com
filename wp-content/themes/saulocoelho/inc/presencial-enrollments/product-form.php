<?php
/**
 * Metabox produto — vínculo formulário pós-inscrição.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'sc_forms_product_metabox' );

function sc_forms_product_metabox() {
	add_meta_box(
		'sc_post_order_form',
		__( 'Questionário pós-inscrição', 'saulocoelho' ),
		'sc_forms_render_product_metabox',
		'product',
		'side',
		'default'
	);
}

function sc_forms_render_product_metabox( $post ) {
	wp_nonce_field( 'sc_forms_product_save', 'sc_forms_product_nonce' );

	$current = sc_forms_product_form_id( $post->ID );
	$forms   = sc_forms_list_forms( 'active' );

	echo '<p class="description">' . esc_html__( 'Opcional. Aluno preenche após o pedido em Minha Conta. Online e presencial.', 'saulocoelho' ) . '</p>';
	echo '<select name="sc_post_order_form_id" id="sc_post_order_form_id" style="width:100%;">';
	echo '<option value="0">' . esc_html__( '— Nenhum —', 'saulocoelho' ) . '</option>';
	foreach ( $forms as $form ) {
		printf(
			'<option value="%d" %s>%s</option>',
			(int) $form->id,
			selected( $current, (int) $form->id, false ),
			esc_html( $form->title )
		);
	}
	echo '</select>';
	echo '<p class="description"><a href="' . esc_url( admin_url( 'admin.php?page=sc-post-order-forms' ) ) . '">' . esc_html__( 'Gerenciar formulários', 'saulocoelho' ) . '</a></p>';
}

add_action( 'save_post_product', 'sc_forms_save_product_metabox', 20, 2 );

function sc_forms_save_product_metabox( $post_id, $post ) {
	if ( ! isset( $_POST['sc_forms_product_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sc_forms_product_nonce'] ) ), 'sc_forms_product_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'manage_options', $post_id ) ) {
		return;
	}

	$form_id = isset( $_POST['sc_post_order_form_id'] ) ? absint( $_POST['sc_post_order_form_id'] ) : 0;
	if ( $form_id ) {
		$form = sc_forms_get_form( $form_id );
		if ( ! $form || $form->status !== 'active' ) {
			$form_id = 0;
		}
	}

	if ( $form_id ) {
		update_post_meta( $post_id, 'sc_post_order_form_id', $form_id );
	} else {
		delete_post_meta( $post_id, 'sc_post_order_form_id' );
	}
}
