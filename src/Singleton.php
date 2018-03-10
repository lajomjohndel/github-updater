<?php
/**
 * Singleton Factory
 *
 * @package   Singleton Factory
 * @author    Andy Fragen
 * @license   MIT
 * @link      https://github.com/afragen/singleton-factory
 */

namespace Fragen;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Fragen\\Singleton' ) ) {

	/**
	 * Class Singleton
	 *
	 * A static proxy for creating Singletons from passed class names.
	 *
	 * @package Fragen
	 */
	final class Singleton {

		/**
		 * Get instance of class.
		 *
		 * @param string               $class_name
		 * @param object               $caller
		 * @param null|array|\stdClass $options
		 *
		 * @return array $instance
		 */
		public static function get_instance( $class_name, $caller = null, $options = null ) {
			static $instance = null;

			$class = get_class( $caller );
			$class = self::get_class( $class_name, $class );

			if ( null === $instance || ! isset( $instance[ $class ] ) ) {
				$instance[ $class ] = new $class( $options );
			}

			// Add calling object.
			$instance[ $class ]->caller = $caller;

			return $instance[ $class ];
		}

		/**
		 * Determine correct class name with namespace and return.
		 *
		 * @param string $class_name
		 * @param string $class
		 *
		 * @return string Namespaced class name.
		 */
		private static function get_class( $class_name, $class ) {
			$reflection      = self::get_reflection( $class );
			$namespace       = $reflection->getNamespaceName();
			$namespace_parts = explode( '\\', $namespace );
			$count           = count( $namespace_parts );
			$classes[ - 1 ]  = null;

			for ( $i = 0; $i < $count; $i ++ ) {
				$classes[ $i ] = ltrim( $classes[ $i - 1 ] . '\\' . $namespace_parts[ $i ], '\\' );
			}

			$classes = array_reverse( $classes );
			foreach ( $classes as $namespace ) {
				$namespaced_class = $namespace . '\\' . $class_name;
				if ( class_exists( $namespaced_class ) ) {
					return $namespaced_class;
				}
			}
		}

		/**
		 * Get ReflectionClass of passed class name.
		 *
		 * @param string $class
		 *
		 * @return \ReflectionClass $reflection
		 */
		private static function get_reflection( $class ) {
			try {
				$reflection = new \ReflectionClass( $class );
			} catch ( \ReflectionException $Exception ) {
				die( '<table>' . $Exception->xdebug_message . '</table>' );
			}

			return $reflection;
		}
	}
}
