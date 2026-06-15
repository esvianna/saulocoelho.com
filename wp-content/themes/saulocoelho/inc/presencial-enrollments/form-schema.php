<?php
/**
 * Schema do questionário pós-inscrição (coaching-terapia-2026-07).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Definição de campos do formulário reutilizável.
 *
 * @return array<string, array<string, mixed>>
 */
function sc_presencial_get_form_schema() {
	return array(
		'sections' => array(
			array(
				'id'    => 'seus_dados',
				'title' => __( 'Seus dados', 'saulocoelho' ),
			),
			array(
				'id'    => 'momento_atual',
				'title' => __( 'Momento atual', 'saulocoelho' ),
			),
			array(
				'id'    => 'sobre_voce',
				'title' => __( 'Sobre você', 'saulocoelho' ),
			),
			array(
				'id'    => 'objetivos',
				'title' => __( 'Objetivos com a formação', 'saulocoelho' ),
			),
		),
		'fields'   => array(
			array(
				'key'         => 'full_name',
				'section'     => 'seus_dados',
				'label'       => __( 'Nome completo', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => true,
			),
			array(
				'key'         => 'birth_date',
				'section'     => 'seus_dados',
				'label'       => __( 'Data de nascimento', 'saulocoelho' ),
				'type'        => 'date',
				'required'    => true,
			),
			array(
				'key'         => 'gender',
				'section'     => 'seus_dados',
				'label'       => __( 'Qual o sexo?', 'saulocoelho' ),
				'type'        => 'select',
				'required'    => true,
				'options'     => array(
					'masculino' => __( 'Masculino', 'saulocoelho' ),
					'feminino'  => __( 'Feminino', 'saulocoelho' ),
				),
			),
			array(
				'key'         => 'phone_whatsapp',
				'section'     => 'seus_dados',
				'label'       => __( 'Telefone (preferência WhatsApp)', 'saulocoelho' ),
				'type'        => 'tel',
				'required'    => true,
			),
			array(
				'key'          => 'referral_source',
				'section'      => 'seus_dados',
				'label'        => __( 'Como ficou sabendo dessa formação?', 'saulocoelho' ),
				'type'         => 'select',
				'required'     => true,
				'options'      => array(
					'instagram'         => 'Instagram',
					'whatsapp'          => 'WhatsApp',
					'indicacao'         => __( 'Indicação', 'saulocoelho' ),
					'evento_presencial' => __( 'Evento presencial', 'saulocoelho' ),
					'site'              => 'Site',
					'other'             => __( 'Outro', 'saulocoelho' ),
				),
				'other_field'  => 'referral_source_other',
			),
			array(
				'key'         => 'referral_source_other',
				'section'     => 'seus_dados',
				'label'       => __( 'Especifique como ficou sabendo', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => false,
				'show_if'     => array( 'field' => 'referral_source', 'value' => 'other' ),
			),
			array(
				'key'         => 'prior_training',
				'section'     => 'seus_dados',
				'label'       => __( 'Você já participou de algum treinamento com Saulo Coelho?', 'saulocoelho' ),
				'type'        => 'select',
				'required'    => true,
				'options'     => array(
					'sim' => __( 'Sim', 'saulocoelho' ),
					'nao' => __( 'Não', 'saulocoelho' ),
				),
			),
			array(
				'key'         => 'prior_training_which',
				'section'     => 'seus_dados',
				'label'       => __( 'Se sim, qual?', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => false,
				'show_if'     => array( 'field' => 'prior_training', 'value' => 'sim' ),
			),
			array(
				'key'          => 'life_areas_change',
				'section'      => 'momento_atual',
				'label'        => __( 'Em qual área da sua vida você sente que mais precisa de mudança hoje?', 'saulocoelho' ),
				'type'         => 'multiselect',
				'required'     => true,
				'options'      => array(
					'emocional'        => __( 'Emocional', 'saulocoelho' ),
					'relacionamentos'  => __( 'Relacionamentos', 'saulocoelho' ),
					'carreira'         => __( 'Carreira', 'saulocoelho' ),
					'vida_financeira'  => __( 'Vida financeira', 'saulocoelho' ),
					'autoconfianca'    => __( 'Autoconfiança', 'saulocoelho' ),
					'comunicacao'      => __( 'Comunicação', 'saulocoelho' ),
					'proposito'        => __( 'Propósito', 'saulocoelho' ),
					'lideranca'        => __( 'Liderança', 'saulocoelho' ),
					'saude_energia'    => __( 'Saúde e energia', 'saulocoelho' ),
					'other'            => __( 'Outro', 'saulocoelho' ),
				),
				'other_field'  => 'life_areas_change_other',
			),
			array(
				'key'         => 'life_areas_change_other',
				'section'     => 'momento_atual',
				'label'       => __( 'Outra área (descreva)', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => false,
				'show_if'     => array( 'field' => 'life_areas_change', 'value' => 'other', 'type' => 'includes' ),
			),
			array(
				'key'         => 'why_now',
				'section'     => 'momento_atual',
				'label'       => __( 'O que fez você decidir entrar nessa formação neste momento da sua vida?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'leave_behind',
				'section'     => 'momento_atual',
				'label'       => __( 'O que você sente que precisa deixar para trás para avançar na sua vida?', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => true,
			),
			array(
				'key'         => 'expectations',
				'section'     => 'momento_atual',
				'label'       => __( 'O que você espera viver, aprender ou transformar durante essa formação?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'life_moment_phrase',
				'section'     => 'momento_atual',
				'label'       => __( 'Hoje, como você descreveria seu momento de vida em uma frase?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'blocking_behavior',
				'section'     => 'sobre_voce',
				'label'       => __( 'Qual comportamento seu mais tem te impedido de avançar para o próximo nível da sua vida?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'self_sabotage_situations',
				'section'     => 'sobre_voce',
				'label'       => __( 'Em quais situações você percebe que sabota a si mesmo?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'hard_to_sustain_change',
				'section'     => 'sobre_voce',
				'label'       => __( 'O que você mais deseja mudar em você hoje, mas sente dificuldade de sustentar na prática?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'fear_triggers',
				'section'     => 'sobre_voce',
				'label'       => __( 'Que tipo de situação mais desperta insegurança, medo ou travamento em você?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'belief_to_revisit',
				'section'     => 'sobre_voce',
				'label'       => __( 'Qual crença sobre você mesmo precisa ser revista ou quebrada?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'future_excites_scares',
				'section'     => 'sobre_voce',
				'label'       => __( 'Quando você pensa no seu futuro, o que mais te empolga e o que mais te assusta?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'who_to_become',
				'section'     => 'sobre_voce',
				'label'       => __( 'Quem você precisa se tornar para viver a vida que deseja?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'          => 'desired_results',
				'section'      => 'objetivos',
				'label'        => __( 'Quais resultados você deseja conquistar com essa formação?', 'saulocoelho' ),
				'type'         => 'multiselect',
				'required'     => true,
				'options'      => array(
					'conhecer_melhor'          => __( 'Me conhecer melhor', 'saulocoelho' ),
					'comunicacao'              => __( 'Melhorar minha comunicação', 'saulocoelho' ),
					'ferramentas_coaching'     => __( 'Aprender ferramentas de coaching', 'saulocoelho' ),
					'ajudar_pessoas'           => __( 'Ajudar outras pessoas', 'saulocoelho' ),
					'tornar_coach'             => __( 'Me tornar coach', 'saulocoelho' ),
					'relacionamentos'          => __( 'Melhorar meus relacionamentos', 'saulocoelho' ),
					'clareza_direcao'          => __( 'Ter mais clareza e direção', 'saulocoelho' ),
					'inteligencia_emocional'   => __( 'Desenvolver inteligência emocional', 'saulocoelho' ),
					'romper_bloqueios'         => __( 'Romper bloqueios internos', 'saulocoelho' ),
					'other'                    => __( 'Outro', 'saulocoelho' ),
				),
				'other_field'  => 'desired_results_other',
			),
			array(
				'key'         => 'desired_results_other',
				'section'     => 'objetivos',
				'label'       => __( 'Outro resultado (descreva)', 'saulocoelho' ),
				'type'        => 'text',
				'required'    => false,
				'show_if'     => array( 'field' => 'desired_results', 'value' => 'other', 'type' => 'includes' ),
			),
			array(
				'key'         => 'coaching_use_intent',
				'section'     => 'objetivos',
				'label'       => __( 'Você deseja usar o Coaching Comportamental apenas para sua transformação pessoal ou também profissionalmente?', 'saulocoelho' ),
				'type'        => 'select',
				'required'    => true,
				'options'     => array(
					'apenas_pessoal'        => __( 'Apenas para minha transformação pessoal', 'saulocoelho' ),
					'principalmente_pessoal' => __( 'Principalmente pessoal, mas também profissionalmente', 'saulocoelho' ),
					'aplicar_profissionalmente' => __( 'Quero aplicar profissionalmente', 'saulocoelho' ),
					'ainda_nao_sei'         => __( 'Ainda não sei', 'saulocoelho' ),
				),
			),
			array(
				'key'         => 'end_feeling',
				'section'     => 'objetivos',
				'label'       => __( 'Ao final da formação, como você quer se sentir?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
			array(
				'key'         => 'success_criteria',
				'section'     => 'objetivos',
				'label'       => __( 'Ao final da formação, o que precisará ter acontecido para você dizer: “valeu a pena estar aqui”?', 'saulocoelho' ),
				'type'        => 'textarea',
				'required'    => true,
			),
		),
	);
}

/**
 * Valida POST e retorna array de erros ou responses sanitizadas.
 *
 * @return array{errors: string[], responses: array<string, mixed>}
 */
function sc_presencial_validate_form_submission( array $post_data ) {
	$schema   = sc_presencial_get_form_schema();
	$errors   = array();
	$response = array();

	foreach ( $schema['fields'] as $field ) {
		$key  = $field['key'];
		$type = $field['type'];
		$raw  = $post_data[ $key ] ?? null;

		if ( ! empty( $field['show_if'] ) && ! sc_presencial_field_visible( $field['show_if'], $post_data ) ) {
			continue;
		}

		if ( $type === 'multiselect' ) {
			$raw = isset( $post_data[ $key ] ) && is_array( $post_data[ $key ] ) ? $post_data[ $key ] : array();
			$raw = array_map( 'sanitize_text_field', wp_unslash( $raw ) );
			$raw = array_values( array_filter( $raw, static function ( $v ) {
				return $v !== '';
			} ) );
			if ( ! empty( $field['required'] ) && empty( $raw ) ) {
				$errors[] = sprintf(
					/* translators: %s: field label */
					__( 'Preencha o campo: %s', 'saulocoelho' ),
					$field['label']
				);
				continue;
			}
			$response[ $key ] = $raw;
			continue;
		}

		if ( is_array( $raw ) ) {
			$raw = '';
		}
		$raw = is_string( $raw ) ? wp_unslash( $raw ) : '';

		if ( $type === 'textarea' ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( $type === 'date' ) {
			$value = sanitize_text_field( $raw );
			if ( $value && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
				$errors[] = sprintf( __( 'Data inválida em: %s', 'saulocoelho' ), $field['label'] );
				continue;
			}
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( ! empty( $field['required'] ) && $value === '' ) {
			$errors[] = sprintf( __( 'Preencha o campo: %s', 'saulocoelho' ), $field['label'] );
			continue;
		}

		if ( $type === 'select' && $value !== '' && ! empty( $field['options'] ) && ! isset( $field['options'][ $value ] ) ) {
			$errors[] = sprintf( __( 'Opção inválida em: %s', 'saulocoelho' ), $field['label'] );
			continue;
		}

		if ( $value !== '' ) {
			$response[ $key ] = $value;
		}
	}

	// Validação condicional "Outro" e "Se sim, qual?"
	if ( ( $post_data['referral_source'] ?? '' ) === 'other' && trim( (string) ( $post_data['referral_source_other'] ?? '' ) ) === '' ) {
		$errors[] = __( 'Descreva como ficou sabendo da formação.', 'saulocoelho' );
	}
	if ( is_array( $post_data['life_areas_change'] ?? null ) && in_array( 'other', $post_data['life_areas_change'], true )
		&& trim( (string) ( $post_data['life_areas_change_other'] ?? '' ) ) === '' ) {
		$errors[] = __( 'Descreva a outra área da vida.', 'saulocoelho' );
	}
	if ( is_array( $post_data['desired_results'] ?? null ) && in_array( 'other', $post_data['desired_results'], true )
		&& trim( (string) ( $post_data['desired_results_other'] ?? '' ) ) === '' ) {
		$errors[] = __( 'Descreva o outro resultado desejado.', 'saulocoelho' );
	}
	if ( ( $post_data['prior_training'] ?? '' ) === 'sim' && trim( (string) ( $post_data['prior_training_which'] ?? '' ) ) === '' ) {
		$errors[] = __( 'Informe qual treinamento você já fez com Saulo.', 'saulocoelho' );
	}

	$extra_keys = array( 'referral_source_other', 'life_areas_change_other', 'desired_results_other', 'prior_training_which' );
	foreach ( $extra_keys as $ek ) {
		if ( isset( $post_data[ $ek ] ) && trim( (string) $post_data[ $ek ] ) !== '' ) {
			$response[ $ek ] = sanitize_text_field( wp_unslash( (string) $post_data[ $ek ] ) );
		}
	}

	return array(
		'errors'    => $errors,
		'responses' => $response,
	);
}

/**
 * @param array<string, mixed> $show_if
 * @param array<string, mixed> $post_data
 */
function sc_presencial_field_visible( array $show_if, array $post_data ) {
	$field = $show_if['field'] ?? '';
	$value = $show_if['value'] ?? '';
	$type  = $show_if['type'] ?? 'equals';

	if ( ! $field ) {
		return true;
	}

	$current = $post_data[ $field ] ?? '';
	if ( $type === 'includes' && is_array( $current ) ) {
		return in_array( $value, $current, true );
	}

	return (string) $current === (string) $value;
}
