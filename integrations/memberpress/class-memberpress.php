<?php

defined( 'ABSPATH' ) or exit;

/**
 * Class MB4WP_MemberPress_Integration
 *
 * @ignore
 */
class MB4WP_MemberPress_Integration extends MB4WP_Integration {

	/**
	 * @var string
	 */
	public $name = "MemberPress";

	/**
	 * @var string
	 */
	public $description = "Subscribes people from MemberPress register forms.";


	/**
	 * Add hooks
	 */
	public function add_hooks() {

		if( ! $this->options['implicit'] ) {
			add_action( 'mepr_checkout_before_submit', array( $this, 'output_checkbox' ) );
		}

		// Hook into both WordPress and MemberPress specific actions
		add_action( 'mepr-event-transaction-completed', array( $this, 'subscribe_from_memberpress' ), 5 );
	}



	/**
	 * Subscribe from MemberPress sign-up forms.
	 *
	 * @param MeprTransaction $txn
	 * @return bool
	 */
	public function subscribe_from_memberpress( $txn ) {
		// Handle MeprEvent or MeprTransaction
		$user_id = null;
		$transaction_id = null;
		$transaction = null;

		if (is_object($txn) && get_class($txn) === 'MeprEvent') {
			// Try to get event data safely
			try {
				$rec = $txn->rec;  // Try direct access first
			} catch (Exception $e) {
				try {
					// Fallback to get_object_vars
					$vars = get_object_vars($txn);
					$rec = isset($vars['rec']) ? $vars['rec'] : null;
				} catch (Exception $e) {
					$rec = null;
				}
			}
			
			if ($rec && isset($rec->evt_id) && isset($rec->evt_id_type) && $rec->evt_id_type === 'transactions') {
				$transaction_id = $rec->evt_id;
				try {
					$transaction = new MeprTransaction($transaction_id);
					$user_id = $transaction->user_id;
				} catch (Exception $e) {
					error_log('Error loading transaction: ' . $e->getMessage());
				}
			}
		} elseif (is_object($txn) && property_exists($txn, 'user_id')) {
			// MeprTransaction
			$user_id = $txn->user_id;
			$transaction = $txn;
			$transaction_id = isset($txn->id) ? $txn->id : null;
		} elseif (is_object($txn) && method_exists($txn, 'user')) {
			$user_id = $txn->user()->ID;
			$transaction = $txn;
			$transaction_id = isset($txn->id) ? $txn->id : null;
		}

		if (!$user_id) {
			return false;
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return false;
		}

		$data = array(
			'EMAIL' => $user->user_email,
			'FNAME' => $user->first_name,
			'LNAME' => $user->last_name
		);

		// subscribe using email and name
		return $this->subscribe($data, $transaction_id);

	}

	/**
	 * @return bool
	 */
	public function is_installed() {
		return defined( 'MEPR_VERSION' );
	}

}