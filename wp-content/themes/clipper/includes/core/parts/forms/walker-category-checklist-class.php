<?php
/**
 * Category walker
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Category checklist walker class
 */
class APP_Walker_Category_Checklist extends Walker_Category_Checklist {

	public $extra = array();
	public $item_id = 0;

	/**
	 * Start the element output.
	 *
	 * @see Walker_Category_Checklist::start_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist().
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		if ( empty( $args['taxonomy'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $args['taxonomy'];
		}

		$tax_obj = get_taxonomy( $taxonomy );

		if ( $tax_obj->hierarchical ) {
			$value_field = 'term_id';
		} else {
			$value_field = 'name';
		}

		$name = 'tax_input[' . $taxonomy . ']';

		$args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];

		$classes = in_array( $category->term_id, $args['popular_cats'] ) ? array( 'popular-category' ) : array();
		$classes = apply_filters( 'appthemes_listing_form_term_classes', $classes, $category, $taxonomy, $depth );
		$classes = array_map( 'esc_attr', $classes );
		$classes = implode( ' ', $classes );

		$class = $classes ? ' class="' . $classes . '"' : '';

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		/** This filter is documented in wp-includes/category-template.php */
		if ( ! empty( $args['list_only'] ) ) {
			$aria_cheched = 'false';
			$inner_class = 'category';

			if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
				$inner_class .= ' selected';
				$aria_cheched = 'true';
			}

			$label = apply_filters( 'the_category', $category->name );
			$label = apply_filters( 'appthemes_listing_form_term_label', $label, $category, $taxonomy, $this->item_id );

			$output .= "\n<li' . $class . '>" .
				'<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
				' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
				esc_html( $label ) . '</div>';
		} else {

			$input_args = array(
				'value' => $category->$value_field,
				'type'  => 'checkbox',
				'name'  => $name.'[]',
				'id'    => 'in-'.$taxonomy.'-' . $category->term_id,
			);

			if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
				$input_args['checked'] = 'checked';
			}

			if ( ! empty( $args['disabled'] ) || isset( $this->extra['disabled'] ) ) {
				$input_args['disabled'] = 'disabled';
			}

			$input_args = array_merge( $input_args, $this->extra );

			$label = apply_filters( 'the_category', $category->name );
			$label = apply_filters( 'appthemes_listing_form_term_label', $label, $category, $taxonomy, $this->item_id );

			$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
				'<label class="selectit">' . html( 'input', $input_args ) . ' ' .
				esc_html( $label ) . '</label>';
		}
	}
}
