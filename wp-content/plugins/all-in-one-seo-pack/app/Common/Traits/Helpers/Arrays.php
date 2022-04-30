<?php
namespace AIOSEO\Plugin\Common\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains array specific helper methods.
 *
 * @since 4.1.4
 */
trait Arrays {
	/**
	 * Unsets a given value in a given array.
	 * This should only be used if the given value only appears once in the array.
	 *
	 * @since 4.0.0
	 *
	 * @param  array  $array The array.
	 * @param  string $value The value that needs to be removed from the array.
	 * @return array  $array The filtered array.
	 */
	public function unsetValue( $array, $value ) {
		if ( in_array( $value, $array, true ) ) {
			unset( $array[ array_search( $value, $array, true ) ] );
		};

		return $array;
	}

	/**
	 * Compares two multidimensional arrays to see if they're different.
	 *
	 * @since 4.0.0
	 *
	 * @param  array   $array1 The first array.
	 * @param  array   $array2 The second array.
	 * @return boolean         Whether the arrays are different.
	 */
	public function arraysDifferent( $array1, $array2 ) {
		foreach ( $array1 as $key => $value ) {
			// Check for non-existing values.
			if ( ! isset( $array2[ $key ] ) ) {
				return true;
			}
			if ( is_array( $value ) ) {
				if ( $this->arraysDifferent( $value, $array2[ $key ] ) ) {
					return true;
				};
			} else {
				if ( $value !== $array2[ $key ] ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Checks whether the given array is associative.
	 * Arrays that only have consecutive, sequential numeric keys are numeric.
	 * Otherwise they are associative.
	 *
	 * @since 4.1.4
	 *
	 * @param  array $array The array.
	 * @return bool         Whether the array is associative.
	 */
	public function isArrayAssociative( $array ) {
		return 0 < count( array_filter( array_keys( $array ), 'is_string' ) );
	}

	/**
	 * Checks whether the given array is numeric.
	 *
	 * @since 4.1.4
	 *
	 * @param  array $array The array.
	 * @return bool         Whether the array is numeric.
	 */
	public function isArrayNumeric( $array ) {
		return ! $this->isArrayAssociative( $array );
	}
}