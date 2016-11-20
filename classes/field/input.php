<?php

/**
 * Class for creating input field element HTML
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_Input extends Caldera_Forms_Field_HTML{

	/**
	 * @inheritdoc
	 */
	public static function html( array $field, array $field_structure,array $form ){
		$type = Caldera_Forms_Field_Util::get_type( $field );
		$field_base_id = Caldera_Forms_Field_Util::get_base_id( $field, null, $form );
		$default = $field[ 'config' ][ 'default'];
		$sync = false;
		if( in_array( $type, self::sync_fields() ) ){
			$syncer = Caldera_Forms_Field_Syncfactory::get_object( $form, $field, $field_base_id );
			$sync = $syncer->can_sync();
			$default = $syncer->get_default();
		}

		if( 'text' == $type && !empty( $field['config']['type_override'] ) ){
			$type = $field['config']['type_override'];
		}
		$required = '';

		$field_classes = Caldera_Forms_Field_Util::prepare_field_classes( $field, $form );
		$mask = self::get_mask_string( $field );
		$place_holder = self::place_holder_string( $field );
		$attrs = array(
			'type' => $type,
			'data-field' =>$field[ 'ID'],
			'class' => $field_classes[ 'field' ],
			'id' => $field[ 'ID'],
			'name' => $field_structure['name'],
			'value' => $default,

		);

		if( $field_structure['field_required'] ){
			$required = 'required';
			$attrs[ 'aria-required' ] = 'true';
		}

		if( $sync ){
			$attrs[ 'data-binds' ] = wp_json_encode( $syncer->get_binds() );
			$attrs[ 'data-sync' ] = $default;
		}

		$attr_string = caldera_forms_field_attributes(
			$attrs,
			$field,
			$form
		);

		$aria = self::aria_string( $field_structure );

		return '<input ' .  $place_holder . $mask .  $required . $attr_string   . ' >';

	}

	/**
	 * Defined which fields use sync
	 *
	 * @sine 1.5.0
	 *
	 * @return array
	 */
	protected static function sync_fields(){
		return array(
			'text',
			'email',
			'html'
		);
	}
}